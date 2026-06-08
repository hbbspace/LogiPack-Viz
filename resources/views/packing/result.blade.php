@extends('layouts.app')

@section('title', 'Hasil Penataan - PosLogistik')

@section('styles')
<style>
    #fitnessChart {
        max-height: 300px;
    }
</style>
@endsection

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Hasil Penataan</h1>
                <p class="text-gray-600 mt-1">{{ $packing->name }}</p>
                <p class="text-xs text-gray-400 mt-1">Dibuat: {{ $packing->created_at->format('d/m/Y H:i:s') }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 text-sm rounded-full {{ $packing->volume_utilization >= 80 ? 'bg-green-100 text-green-800' : ($packing->volume_utilization >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    Utilisasi: {{ number_format($packing->volume_utilization, 2) }}%
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Visualisasi 3D --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6 flex flex-col">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Visualisasi 3D</h2>
            <div class="flex-1 w-full" style="min-height: 500px;">
                @if($packing->visualization_file_path)
                    @php
                        $vizPath = $packing->visualization_file_path;
                        if (str_starts_with($vizPath, '/')) {
                            $vizPath = substr($vizPath, 1);
                        }
                    @endphp
                    @if(file_exists(public_path($vizPath)))
                        <iframe src="{{ asset($vizPath) }}" class="w-full h-full border rounded" title="3D Visualization" style="height: 100%; min-height: 500px;"></iframe>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <div class="text-5xl mb-3">📦</div>
                            <p>File visualisasi tidak ditemukan</p>
                            <p class="text-xs mt-2">Path: {{ $vizPath }}</p>
                        </div>
                    @endif
                @else
                <div class="text-center py-12 text-gray-500">
                    <div class="text-5xl mb-3">📦</div>
                    <p>Visualisasi belum tersedia</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Ringkasan --}}
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Ringkasan</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Container:</span>
                        <span class="font-medium">{{ $packing->container->name ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Dimensi Container:</span>
                        <span class="font-medium">{{ $packing->container->length ?? 0 }}x{{ $packing->container->width ?? 0 }}x{{ $packing->container->height ?? 0 }} cm</span>
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
                        <span class="text-gray-600">GA Parameter:</span>
                        <span class="font-medium">{{ $packing->gaParameter->name ?? 'Default' }}</span>
                    </div>
                    @if($packing->execution_time_ms)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Waktu Eksekusi:</span>
                        <span class="font-medium">{{ number_format($packing->execution_time_ms / 1000, 2) }} detik</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Paket Terpasang --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3">Urutan Paket Terpasang</h2>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($packing->placedPackages as $pkg)
                    <div class="border-l-4 border-green-500 pl-3 py-1 text-sm">
                        <div class="font-medium">{{ $pkg->tracking_number }}</div>
                        <div class="text-gray-500 text-xs">
                            Posisi: ({{ $pkg->pivot->position_x }}, {{ $pkg->pivot->position_y }}, {{ $pkg->pivot->position_z }})
                            | Orientasi: {{ $pkg->pivot->orientation }}
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-4">Tidak ada paket terpasang</div>
                    @endforelse
                </div>
            </div>

            {{-- Paket Tidak Terpasang --}}
            @if($packing->unplacedPackages->count() > 0)
            <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4">
                <h2 class="font-semibold text-yellow-800 mb-2">Paket Tidak Terpasang</h2>
                <div class="space-y-1 max-h-32 overflow-y-auto">
                    @foreach($packing->unplacedPackages as $pkg)
                    <div class="text-sm text-yellow-700">• {{ $pkg->tracking_number }}</div>
                    @endforeach
                </div>
                <p class="text-xs text-yellow-600 mt-2">Paket ini dapat diproses ulang pada penataan berikutnya.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Grafik Fitness History --}}
    @if($packing->gaHistories && $packing->gaHistories->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Konvergensi Fitness per Generasi</h2>
        <canvas id="fitnessChart" height="250"></canvas>
        <p class="text-xs text-gray-400 mt-2 text-center">Grafik menunjukkan peningkatan fitness score selama proses Genetic Algorithm</p>
    </div>
    @endif

    {{-- Tombol Navigasi --}}
    <div class="flex justify-between flex-wrap gap-3">
        <a href="{{ route('packing.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg font-medium hover:bg-gray-600 transition">
            Penataan Baru
        </a>
        <a href="{{ route('packing.history') }}" class="bg-pos-blue text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
            Lihat Riwayat
        </a>
    </div>
</div>
@endsection

@section('scripts')
@if($packing->gaHistories && $packing->gaHistories->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fitnessData = @json($packing->gaHistories->pluck('fitness_score'));
        const generations = @json($packing->gaHistories->pluck('generation'));
        
        const ctx = document.getElementById('fitnessChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: generations,
                datasets: [{
                    label: 'Fitness Score',
                    data: fitnessData,
                    borderColor: '#FF0000',
                    backgroundColor: 'rgba(255, 0, 0, 0.05)',
                    borderWidth: 2,
                    pointRadius: 2,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#FF0000',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Fitness: ${context.raw.toFixed(2)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        title: {
                            display: true,
                            text: 'Fitness Score',
                            font: { weight: 'bold' }
                        },
                        beginAtZero: false
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Generasi',
                            font: { weight: 'bold' }
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection