<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContainerController extends Controller
{
    public function index()
    {
        $containers = Container::orderBy('created_at', 'asc')->paginate(15);
        return view('admin.containers.index', compact('containers'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type_code' => 'nullable|string|max:50',
            'length' => 'required|integer|min:1',
            'width' => 'required|integer|min:1',
            'height' => 'required|integer|min:1',
            'weight_max' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);
        
        $volume = $request->length * $request->width * $request->height;
        
        Container::create([
            'name' => $request->name,
            'type_code' => $request->type_code,
            'length' => $request->length,
            'width' => $request->width,
            'height' => $request->height,
            'volume_max' => $volume,
            'weight_max' => $request->weight_max,
            'description' => $request->description,
            'is_active' => true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
        
        return redirect()->route('admin.containers.index')->with('success', 'Container berhasil ditambahkan.');
    }
    
    public function toggle($id)
    {
        $container = Container::findOrFail($id);
        $container->update([
            'is_active' => !$container->is_active,
            'updated_by' => Auth::id(),
        ]);
        
        $status = $container->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Container {$container->name} berhasil {$status}.");
    }
}