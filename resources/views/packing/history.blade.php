@extends('layouts.app')

@section('title', 'Riwayat Penataan - PosLogistik')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Penataan</h1>
        <p class="text-gray-600 mt-1">Daftar semua proses penataan paket yang telah dilakukan</p>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Packing</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Container</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisasi Volume</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisasi Berat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fitness</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($packings as $packing)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $packing->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $packing->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $packing->container->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full {{ $packing->volume_utilization >= 70 ? 'bg-green-100 text-green-800' : ($packing->volume_utilization >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ number_format($packing->volume_utilization, 2) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 text-xs rounded-full {{ $packing->weight_utilization >= 80 ? 'bg-green-100 text-green-800' : ($packing->weight_utilization >= 60 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ number_format($packing->weight_utilization, 2) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($packing->fitness_score, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('packing.result', $packing->id) }}" class="text-pos-blue hover:underline">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            <div class="text-4xl mb-2">📋</div>
                            <p>Belum ada riwayat penataan</p>
                            <a href="{{ route('packing.index') }}" class="text-pos-blue hover:underline mt-2 inline-block">Mulai Penataan →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $packings->links() }}
        </div>
    </div>
</div>
@endsection