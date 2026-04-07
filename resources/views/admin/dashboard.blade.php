@extends('layouts.app')

@section('title', 'Admin Dashboard - PosLogistik')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-600 mt-1">Overview seluruh sistem PosLogistik</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
            <p class="text-sm opacity-90">Total Paket</p>
            <p class="text-3xl font-bold">{{ $totalPackages ?? 0 }}</p>
        </div>
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg shadow p-6 text-white">
            <p class="text-sm opacity-90">Paket Pending</p>
            <p class="text-3xl font-bold">{{ $pendingPackages ?? 0 }}</p>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
            <p class="text-sm opacity-90">Total Packing</p>
            <p class="text-3xl font-bold">{{ $totalPackings ?? 0 }}</p>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow p-6 text-white">
            <p class="text-sm opacity-90">Total Delivery</p>
            <p class="text-3xl font-bold">{{ $totalDeliveries ?? 0 }}</p>
        </div>
    </div>

    @if(isset($packingsByBranch) && $packingsByBranch->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Statistik per Branch</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Packing</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rata-rata Utilisasi Volume</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($packingsByBranch as $stat)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $stat->branch->city ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $stat->total }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($stat->avg_util, 2) }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection