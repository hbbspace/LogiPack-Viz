@extends('layouts.app')

@section('title', 'Detail Batch - PosLogistik')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Batch Import</h1>
                <p class="text-gray-600 mt-1">{{ $batchImport->original_name }}</p>
                <p class="text-xs text-gray-400">Upload: {{ $batchImport->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
            <a href="{{ route('upload.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-600 transition">
                ← Kembali
            </a>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-lg shadow p-6 text-white">
            <p class="text-sm opacity-90">Total Paket</p>
            <p class="text-3xl font-bold">{{ $batchImport->total_packages }}</p>
        </div>
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-lg shadow p-6 text-white">
            <p class="text-sm opacity-90">Pending</p>
            <p class="text-3xl font-bold">{{ $batchImport->packages->where('status', 'pending')->count() }}</p>
        </div>
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-lg shadow p-6 text-white">
            <p class="text-sm opacity-90">Packed</p>
            <p class="text-3xl font-bold">{{ $batchImport->packages->where('status', 'packed')->count() }}</p>
        </div>
    </div>

    {{-- Daftar Paket --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Daftar Paket</h2>
        </div>
        
        @if($batchImport->packages->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tracking Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dimensi (PxLxT)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volume</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Berat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($batchImport->packages as $package)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-mono text-gray-900">{{ $package->tracking_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $package->length }} x {{ $package->width }} x {{ $package->height }} cm</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($package->volume) }} cm³</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($package->weight, 2) }} kg</td>
                        <td class="px-6 py-4">
                            @if($package->status === 'pending')
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Packed</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12 text-gray-500">
            <div class="text-5xl mb-3">📦</div>
            <p>Tidak ada paket dalam batch ini</p>
        </div>
        @endif
    </div>
</div>
@endsection