<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Container;
use App\Models\Packing;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Gunakan Auth::user() bukan auth()->user()
        $user = Auth::user();
        
        // Cek jika user tidak login
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Jika user adalah admin, tampilkan semua data
        if ($user->isAdmin()) {
            $pendingPackages = Package::where('status', 'pending')->count();
            $totalPackings = Packing::count();
            $availableContainers = Container::where('is_active', true)->count();
            $recentPackings = Packing::with('container', 'branch')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        } else {
            // User biasa hanya melihat data di branch-nya
            // Gunakan optional() untuk menghindari error jika branch_id null
            $branchId = $user->branch_id;
            
            if ($branchId) {
                $pendingPackages = Package::where('branch_origin_id', $branchId)
                    ->where('status', 'pending')
                    ->count();
                $totalPackings = Packing::where('branch_id', $branchId)->count();
                $recentPackings = Packing::where('branch_id', $branchId)
                    ->with('container')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            } else {
                $pendingPackages = 0;
                $totalPackings = 0;
                $recentPackings = collect();
            }
            $availableContainers = Container::where('is_active', true)->count();
        }
        
        return view('dashboard', compact('pendingPackages', 'totalPackings', 'availableContainers', 'recentPackings'));
    }
    
    public function adminDashboard()
    {
        $totalPackages = Package::count();
        $pendingPackages = Package::where('status', 'pending')->count();
        $totalPackings = Packing::count();
        $totalDeliveries = \App\Models\Delivery::count();
        
        $packingsByBranch = Packing::with('branch')
            ->selectRaw('branch_id, COUNT(*) as total, AVG(volume_utilization) as avg_util')
            ->groupBy('branch_id')
            ->get();
        
        return view('admin.dashboard', compact('totalPackages', 'pendingPackages', 'totalPackings', 'totalDeliveries', 'packingsByBranch'));
    }
}