<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GaParameter;
use App\Models\Packing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GaParameterController extends Controller
{
    public function index()
    {
        $parameters = GaParameter::withCount('packings')
            ->withAvg('packings as avg_volume_utilization', 'volume_utilization')
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'asc')
            ->paginate(15);
        
        return view('admin.ga-parameters.index', compact('parameters'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:ga_parameters,name',
            'population_size' => 'required|integer|min:10|max:500',
            'generation_limit' => 'required|integer|min:10|max:1000',
            'crossover_rate' => 'required|numeric|min:0|max:1',
            'mutation_rate' => 'required|numeric|min:0|max:1',
        ]);
        
        GaParameter::create([
            'name' => $request->name,
            'population_size' => $request->population_size,
            'generation_limit' => $request->generation_limit,
            'crossover_rate' => $request->crossover_rate,
            'mutation_rate' => $request->mutation_rate,
            'is_active' => false,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
        
        return redirect()->route('admin.ga-parameters.index')->with('success', 'Parameter GA berhasil ditambahkan.');
    }
    
    public function activate($id)
    {
        $parameter = GaParameter::findOrFail($id);
        
        DB::transaction(function () use ($parameter) {
            // Nonaktifkan semua parameter lain
            GaParameter::where('is_active', true)->update(['is_active' => false]);
            // Aktifkan parameter ini
            $parameter->update(['is_active' => true]);
        });
        
        return redirect()->route('admin.ga-parameters.index')->with('success', "Parameter {$parameter->name} berhasil diaktifkan.");
    }
}