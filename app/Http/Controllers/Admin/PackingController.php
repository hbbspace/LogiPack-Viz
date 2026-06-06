<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Packing;

class PackingController extends Controller
{
    public function index()
    {
        $packings = Packing::with(['user', 'user.branch', 'container', 'gaParameter'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.packings.index', compact('packings'));
    }
}