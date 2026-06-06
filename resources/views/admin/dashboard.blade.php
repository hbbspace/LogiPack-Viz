@extends('layouts.admin')

@section('title', 'Admin Dashboard - PosLogistik')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-gray-600 mt-1">Overview sistem PosLogistik</p>
    </div>

    {{-- Statistik Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total User Aktif</p>
                    <p class="text-3xl font-bold">{{ $totalActiveUsers }}</p>
                </div>
                <div class="text-4xl">👥</div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Container Aktif</p>
                    <p class="text-3xl font-bold">{{ $activeContainers }}</p>
                </div>
                <div class="text-4xl">🚚</div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Rata-rata Utilisasi Volume</p>
                    <p class="text-3xl font-bold">{{ number_format($avgVolumeUtilization, 1) }}%</p>
                </div>
                <div class="text-4xl">📊</div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Packing</p>
                    <p class="text-3xl font-bold">{{ $totalPackings }}</p>
                </div>
                <div class="text-4xl">📦</div>
            </div>
        </div>
    </div>

    {{-- Ringkasan per User --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Ringkasan per User</h2>
        </div>
        
        @if($usersSummary->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Batch</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Paket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Packing</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rata-rata Utilisasi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($usersSummary as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->branch->city ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $user->batch_imports_count }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $user->packages_count }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $user->packings_count }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->avg_volume_utilization >= 80 ? 'bg-green-100 text-green-800' : ($user->avg_volume_utilization >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ number_format($user->avg_volume_utilization, 1) }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">Belum ada data packing</div>
        @endif
    </div>
</div>
@endsection