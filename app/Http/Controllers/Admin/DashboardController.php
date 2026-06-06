<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Container;
use App\Models\Packing;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total user aktif (bukan admin)
        $totalActiveUsers = User::where('is_active', true)
            ->where('level', 'user')
            ->count();
        
        // Container aktif
        $activeContainers = Container::where('is_active', true)->count();
        
        // Rata-rata utilisasi volume dari semua packing
        $avgVolumeUtilization = Packing::avg('volume_utilization') ?? 0;
        
        // Total packing
        $totalPackings = Packing::count();
        
        // Ringkasan per user (non-admin)
        $usersSummary = User::where('level', 'user')
            ->with('branch')
            ->withCount([
                'batchImports as batch_imports_count',
                'packages as packages_count',
                'packings as packings_count'
            ])
            ->withAvg('packings as avg_volume_utilization', 'volume_utilization')
            ->orderBy('packings_count', 'desc')
            ->get();
        
        return view('admin.dashboard', compact(
            'totalActiveUsers',
            'activeContainers',
            'avgVolumeUtilization',
            'totalPackings',
            'usersSummary'
        ));
    }
}