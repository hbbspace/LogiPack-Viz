<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Container;
use App\Models\Packing;
use App\Models\BatchImport;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Jika user adalah admin, tampilkan admin dashboard
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        // User biasa: dashboard berdasarkan batch import miliknya
        $batchImports = BatchImport::where('user_id', $user->id)->get();
        $totalPackages = Package::whereHas('batchImport', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->count();
        
        $pendingPackages = Package::whereHas('batchImport', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('status', 'pending')->count();
        
        $totalPackings = Packing::where('user_id', $user->id)->count();
        $availableContainers = Container::where('is_active', true)->count();
        
        $recentPackings = Packing::where('user_id', $user->id)
            ->with('container')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        return view('dashboard', compact(
            'totalPackages', 'pendingPackages', 'totalPackings', 
            'availableContainers', 'recentPackings', 'batchImports'
        ));
    }
    
    public function adminDashboard()
    {
        $totalPackages = Package::count();
        $pendingPackages = Package::where('status', 'pending')->count();
        $totalPackings = Packing::count();
        $totalBatchImports = BatchImport::count();
        $totalUsers = User::count();
        
        // GA Performance
        $avgFitness = Packing::avg('fitness_score');
        $avgVolumeUtil = Packing::avg('volume_utilization');
        $avgWeightUtil = Packing::avg('weight_utilization');
        
        // Packing per user
        $packingsByUser = Packing::with('user')
            ->selectRaw('user_id, COUNT(*) as total, AVG(volume_utilization) as avg_util')
            ->groupBy('user_id')
            ->get();
        
        // Packing per GA Parameter
        $packingsByGaParam = Packing::with('gaParameter')
            ->selectRaw('ga_parameter_id, COUNT(*) as total, AVG(fitness_score) as avg_fitness')
            ->groupBy('ga_parameter_id')
            ->get();
        
        return view('admin.dashboard', compact(
            'totalPackages', 'pendingPackages', 'totalPackings', 
            'totalBatchImports', 'totalUsers', 'avgFitness', 
            'avgVolumeUtil', 'avgWeightUtil', 'packingsByUser',
            'packingsByGaParam'
        ));
    }
}