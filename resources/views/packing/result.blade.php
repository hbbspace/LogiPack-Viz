@extends('layouts.app')

@section('title', 'Hasil Penataan - PosLogistik')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Hasil Penataan</h1>
                <p class="text-gray-600 mt-1">{{ $packing->name }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 text-sm rounded-full {{ $packing->volume_utilization >= 70 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    Utilisasi: {{ number_format($packing->volume_utilization, 2) }}%
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Visualisasi 3D</h2>
            @if($packing->visualization_file_path && file_exists(public_path($packing->visualization_file_path)))
                <iframe src="{{ asset($packing->visualization_file_path) }}" class="w-full h-96 border rounded"></iframe>
            @else
                <div class="text-center py-12 text-gray-500">
                    <div class="text-5xl mb-3">📦</div>
                    <p>Visualisasi belum tersedia</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Ringkasan</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Container:</span>
                        <span class="font-medium">{{ $packing->container->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Paket Terpasang:</span>
                        <span class="font-medium text-green-600">{{ $packing->placedPackages->count() }} / {{ $packing->placedPackages->count() + $packing->unplacedPackages->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Utilisasi Volume:</span>
                        <span class="font-medium">{{ number_format($packing->volume_utilization, 2) }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Utilisasi Berat:</span>
                        <span class="font-medium">{{ number_format($packing->weight_utilization, 2) }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fitness Score:</span>
                        <span class="font-medium">{{ number_format($packing->fitness_score, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pusat Massa:</span>
                        <span class="font-medium">({{ number_format($packing->center_of_gravity_x, 2) }}, {{ number_format($packing->center_of_gravity_y, 2) }}, {{ number_format($packing->center_of_gravity_z, 2) }})</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Paket Terpasang</h2>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @foreach($packing->placedPackages as $pkg)
                    <div class="border-l-4 border-green-500 pl-3 py-1 text-sm">
                        <div class="font-medium">{{ $pkg->tracking_number }}</div>
                        <div class="text-gray-500 text-xs">Posisi: ({{ $pkg->pivot->position_x }}, {{ $pkg->pivot->position_y }}, {{ $pkg->pivot->position_z }})</div>
                    </div>
                    @endforeach
                </div>
            </div>

            @if($packing->unplacedPackages->count() > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4">
                <h2 class="font-semibold text-yellow-800 mb-2">Paket Tidak Terpasang</h2>
                <div class="space-y-1">
                    @foreach($packing->unplacedPackages as $pkg)
                    <div class="text-sm text-yellow-700">• {{ $pkg->tracking_number }}</div>
                    @endforeach
                </div>
                <p class="text-xs text-yellow-600 mt-2">Paket ini dapat diproses ulang pada penataan berikutnya.</p>
            </div>
            @endif
        </div>
    </div>

    <div class="flex justify-between">
        <a href="{{ route('packing.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg font-medium hover:bg-gray-600 transition">
            Penataan Baru
        </a>
        <a href="{{ route('packing.history') }}" class="bg-pos-blue text-white px-6 py-2 rounded-lg font-medium hover-bg-pos-blue transition">
            Lihat Riwayat
        </a>
    </div>
</div>
@endsection