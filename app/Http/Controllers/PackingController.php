<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\Package;
use App\Models\Packing;
use App\Models\PackingPackage;
use App\Models\PackingGaHistory;
use App\Models\BatchImport;
use App\Models\GaParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PackingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $containers = Container::where('is_active', true)->get();
        $activeGaParam = GaParameter::getActive() ?? GaParameter::getDefault();
        
        // Ambil semua batch import user yang memiliki package pending
        $batchImports = BatchImport::where('user_id', $user->id)
            ->with(['packages' => function($q) {
                $q->where('status', 'pending');
            }])
            ->get()
            ->filter(function($batch) {
                return $batch->packages->count() > 0;
            })
            ->values();
        
        return view('packing.index', compact('containers', 'batchImports', 'activeGaParam'));
    }
    
    public function process(Request $request)
    {
        $request->validate([
            'container_id' => 'required|exists:containers,id',
            'package_ids' => 'required|array|min:1',
            'package_ids.*' => 'exists:packages,id',
        ]);
        
        $container = Container::findOrFail($request->container_id);
        $packages = Package::whereIn('id', $request->package_ids)
            ->where('status', 'pending')
            ->get();
        
        if ($packages->count() === 0) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Paket yang dipilih sudah diproses atau tidak valid'], 400);
            }
            return back()->with('error', 'Paket yang dipilih sudah diproses atau tidak valid');
        }
        
        $activeGaParam = GaParameter::getActive() ?? GaParameter::getDefault();
        
        if (!$activeGaParam) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => 'Tidak ada parameter GA yang aktif'], 400);
            }
            return back()->with('error', 'Tidak ada parameter GA yang aktif. Silakan hubungi admin.');
        }
        
        $apiData = [
            'container' => [
                'length' => (float) $container->length,
                'width' => (float) $container->width,
                'height' => (float) $container->height,
                'max_weight' => (float) $container->weight_max,
            ],
            'packages' => $packages->map(function($pkg) {
                return [
                    'id' => $pkg->tracking_number,
                    'length' => (float) $pkg->length,
                    'width' => (float) $pkg->width,
                    'height' => (float) $pkg->height,
                    'weight' => (float) $pkg->weight,
                ];
            })->toArray(),
            'ga_params' => [
                'population_size' => $activeGaParam->population_size,
                'generations' => $activeGaParam->generation_limit,
                'crossover_rate' => (float) $activeGaParam->crossover_rate,
                'mutation_rate' => (float) $activeGaParam->mutation_rate,
            ],
        ];
        
        Log::info('Sending to API:', ['api_data' => $apiData]);
        
        try {
            $response = Http::timeout(config('ga.timeout', 300))
                ->post(config('ga.api_url', 'http://localhost:8001') . '/pack', $apiData);
            
            Log::info('API Response Status: ' . $response->status());
            
            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('API Response keys: ' . json_encode(array_keys($result)));
                
                $user = Auth::user();
                
                DB::beginTransaction();
                
                try {
                    // 1. Simpan ke tabel packings
                    $packingData = [
                        'name' => 'Packing ' . now()->format('Y-m-d H:i:s'),
                        'volume_utilization' => $result['volume_utilization'] ?? 0,
                        'weight_utilization' => $result['weight_utilization'] ?? 0,
                        'fitness_score' => $result['fitness'] ?? 0,
                        'chromosome' => isset($result['chromosome']) ? json_encode($result['chromosome']) : null,
                        'visualization_file_path' => $result['visualization_html'] ?? null,
                        'execution_time_ms' => isset($result['execution_time_seconds']) ? $result['execution_time_seconds'] * 1000 : null,
                        'notes' => $result['message'] ?? null,
                        'container_id' => $container->id,
                        'user_id' => $user->id,
                        'ga_parameter_id' => $activeGaParam->id,
                        'created_by' => $user->id,
                    ];
                    
                    Log::info('Attempting to save packing with data:', $packingData);
                    
                    $packing = Packing::create($packingData);
                    
                    Log::info('Packing saved successfully with ID: ' . $packing->id);
                    
                    // 2. Simpan placed packages ke packing_packages
                    $placedPackages = $result['placed_packages'] ?? [];
                    foreach ($placedPackages as $placed) {
                        $package = Package::where('tracking_number', $placed['id'])->first();
                        if ($package) {
                            PackingPackage::create([
                                'packing_id' => $packing->id,
                                'package_id' => $package->id,
                                'is_placed' => true,
                                'position_x' => $placed['x'] ?? 0,
                                'position_y' => $placed['y'] ?? 0,
                                'position_z' => $placed['z'] ?? 0,
                                'orientation' => $placed['orientation'] ?? 1,
                            ]);
                            $package->markAsPacked();
                            Log::info('Package ' . $package->tracking_number . ' marked as packed');
                        }
                    }
                    
                    // 3. Simpan unplaced packages ke packing_packages
                    $unplacedPackages = $result['unplaced_packages'] ?? [];
                    foreach ($unplacedPackages as $unplacedId) {
                        $package = Package::where('tracking_number', $unplacedId)->first();
                        if ($package) {
                            PackingPackage::create([
                                'packing_id' => $packing->id,
                                'package_id' => $package->id,
                                'is_placed' => false,
                            ]);
                            Log::info('Package ' . $package->tracking_number . ' marked as unplaced');
                        }
                    }
                    
                    // 4. Simpan GA History (tanpa chromosome)
                    $history = $result['history'] ?? [];
                    foreach ($history as $genData) {
                        PackingGaHistory::create([
                            'packing_id' => $packing->id,
                            'generation' => $genData['generation'],
                            'fitness_score' => $genData['best_fitness'] ?? 0,
                            'volume_utilization' => $genData['best_volume_utilization'] ?? 0,
                        ]);
                    }
                    Log::info('Saved ' . count($history) . ' GA history records');
                    
                    DB::commit();
                    
                    // Response untuk AJAX request
                    if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                        return response()->json([
                            'success' => true,
                            'redirect_url' => route('packing.result', $packing->id),
                            'packing_id' => $packing->id
                        ]);
                    }
                    
                    return redirect()->route('packing.result', $packing->id)
                        ->with('success', "Proses penataan berhasil! {$result['num_placed']}/{$result['total_packages']} paket terisi.");
                    
                } catch (\Exception $e) {
                    DB::rollback();
                    Log::error('Save Packing Error: ' . $e->getMessage());
                    Log::error('Save Packing Trace: ' . $e->getTraceAsString());
                    
                    if ($request->ajax()) {
                        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
                    }
                    return back()->with('error', 'Gagal menyimpan hasil penataan: ' . $e->getMessage());
                }
            }
            
            $errorMsg = $response->json()['detail'] ?? $response->json()['message'] ?? 'Unknown error';
            Log::error('Packing API Error Response: ' . $errorMsg);
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $errorMsg], 500);
            }
            return back()->with('error', 'Gagal memproses penataan: ' . $errorMsg);
            
        } catch (\Exception $e) {
            Log::error('Packing API Exception: ' . $e->getMessage());
            Log::error('Packing API Trace: ' . $e->getTraceAsString());
            
            if ($request->ajax()) {
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function result($id)
    {
        $packing = Packing::with([
            'container', 
            'placedPackages', 
            'unplacedPackages',
            'gaParameter',
            'gaHistories' => function($q) {
                $q->orderBy('generation', 'asc');
            }
        ])->findOrFail($id);

        $user = Auth::user();
    
        if (!$user->isAdmin() && $packing->user_id !== $user->id) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke hasil penataan ini.');
        }
        
        return view('packing.result', compact('packing'));
    }
    
    public function history()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        if ($user->isAdmin()) {
            $packings = Packing::with(['container', 'user', 'gaParameter'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            $packings = Packing::where('user_id', $user->id)
                ->with(['container', 'gaParameter'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }
        
        return view('packing.history', compact('packings'));
    }
}