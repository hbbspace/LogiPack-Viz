<?php

namespace App\Http\Controllers;

use App\Models\Container;
use App\Models\Package;
use App\Models\Packing;
use App\Models\PackingPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PackingController extends Controller
{
    public function index()
    {
        $containers = Container::where('is_active', true)->get();
        $user = Auth::user();
        
        // Cek jika user tidak login atau tidak punya branch
        if (!$user || !$user->branch_id) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke branch manapun');
        }
        
        $branchId = $user->branch_id;
        
        // Hanya tampilkan paket yang statusnya pending dan berasal dari branch user
        $packages = Package::where('branch_origin_id', $branchId)
            ->where('status', 'pending')
            ->with('branchDestination')
            ->get();
        
        return view('packing.index', compact('containers', 'packages'));
    }
    
    public function process(Request $request)
    {
        $request->validate([
            'container_id' => 'required|exists:containers,id',
            'package_ids' => 'required|array|min:1',
            'package_ids.*' => 'exists:packages,id',
        ]);
        
        $container = Container::findOrFail($request->container_id);
        $packages = Package::whereIn('id', $request->package_ids)->get();
        
        $numPackages = $packages->count();
        $gaParams = $this->selectGAPreset($numPackages);
        
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
            'ga_params' => $gaParams,
        ];
        
        try {
            $response = Http::timeout(config('ga.timeout', 60))
                ->post(config('ga.api_url') . '/pack', $apiData);
            
            if ($response->successful()) {
                $result = $response->json();
                
                $user = Auth::user();
                
                $packing = Packing::create([
                    'name' => 'Packing ' . now()->format('Y-m-d H:i:s'),
                    'volume_utilization' => $result['volume_utilization'],
                    'weight_utilization' => $result['weight_utilization'],
                    'fitness_score' => $result['fitness'],
                    'center_of_gravity_x' => $result['center_of_gravity'][0] ?? null,
                    'center_of_gravity_y' => $result['center_of_gravity'][1] ?? null,
                    'center_of_gravity_z' => $result['center_of_gravity'][2] ?? null,
                    'visualization_file_path' => $result['visualization_html'] ?? null,
                    'raw_result' => json_encode($result),
                    'algorithm_params' => json_encode($gaParams),
                    'container_id' => $container->id,
                    'branch_id' => $user->branch_id,
                    'user_id' => $user->id,
                    'created_by' => $user->id,
                ]);
                
                foreach ($result['placed_packages'] as $placed) {
                    $package = Package::where('tracking_number', $placed['id'])->first();
                    if ($package) {
                        PackingPackage::create([
                            'packing_id' => $packing->id,
                            'package_id' => $package->id,
                            'is_placed' => true,
                            'position_x' => $placed['x'],
                            'position_y' => $placed['y'],
                            'position_z' => $placed['z'],
                            'orientation' => $placed['orientation'],
                        ]);
                        $package->markAsPacked();
                    }
                }
                
                foreach ($result['unplaced_packages'] as $unplacedId) {
                    $package = Package::where('tracking_number', $unplacedId)->first();
                    if ($package) {
                        PackingPackage::create([
                            'packing_id' => $packing->id,
                            'package_id' => $package->id,
                            'is_placed' => false,
                        ]);
                    }
                }
                
                return redirect()->route('packing.result', $packing->id)
                    ->with('success', 'Proses penataan berhasil!');
            }
            
            $errorMsg = $result['message'] ?? 'Unknown error';
            Log::error('Packing API Error Response: ' . $errorMsg);
            return back()->with('error', 'Gagal memproses penataan: ' . $errorMsg);
            
        } catch (\Exception $e) {
            Log::error('Packing API Exception: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function result($id)
    {
        $packing = Packing::with(['container', 'placedPackages', 'unplacedPackages'])->findOrFail($id);
        
        return view('packing.result', compact('packing'));
    }
    
    public function history()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        if ($user->isAdmin()) {
            $packings = Packing::with(['container', 'branch', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            $branchId = $user->branch_id;
            $packings = Packing::where('branch_id', $branchId)
                ->with('container')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }
        
        return view('packing.history', compact('packings'));
    }
    
    private function selectGAPreset($numPackages)
    {
        $presets = config('ga.presets');
        
        foreach ($presets as $preset) {
            if ($numPackages <= $preset['max_packages']) {
                return [
                    'population_size' => $preset['population_size'],
                    'generations' => $preset['generations'],
                    'crossover_rate' => $preset['crossover_rate'],
                    'mutation_rate' => $preset['mutation_rate'],
                ];
            }
        }
        
        return $presets['large'];
    }
}