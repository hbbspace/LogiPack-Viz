@extends('layouts.app')

@section('title', 'Dashboard - PosLogistik')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ auth()->user()->name }}!</h1>
        <p class="text-gray-600 mt-2">Sistem Penataan Paket 3D untuk pengiriman logistik Pos Indonesia</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-r from-pos-red to-red-600 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Paket Pending</p>
                    <p class="text-3xl font-bold">{{ $pendingPackages ?? 0 }}</p>
                </div>
                <div class="text-4xl">📦</div>
            </div>
            <a href="{{ route('packing.index') }}" class="mt-4 inline-block text-sm underline opacity-90 hover:opacity-100">Proses Penataan →</a>
        </div>

        <div class="bg-gradient-to-r from-pos-blue to-blue-700 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Packing Tersimpan</p>
                    <p class="text-3xl font-bold">{{ $totalPackings ?? 0 }}</p>
                </div>
                <div class="text-4xl">📊</div>
            </div>
            <a href="{{ route('packing.history') }}" class="mt-4 inline-block text-sm underline opacity-90 hover:opacity-100">Lihat Riwayat →</a>
        </div>

        <div class="bg-gradient-to-r from-gray-700 to-gray-900 rounded-lg shadow p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Container Tersedia</p>
                    <p class="text-3xl font-bold">{{ $availableContainers ?? 0 }}</p>
                </div>
                <div class="text-4xl">🚚</div>
            </div>
        </div>
    </div>

    @if(isset($recentPackings) && $recentPackings->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Packing Terbaru</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Container</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisasi Volume</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fitness</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentPackings as $packing)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $packing->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $packing->container->name }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full {{ $packing->volume_utilization >= 70 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ number_format($packing->volume_utilization, 2) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($packing->fitness_score, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('packing.result', $packing->id) }}" class="text-pos-blue hover:underline">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection