@extends('layouts.admin')

@section('title', 'GA Parameter Management - Admin')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">GA Parameter Management</h1>
                <p class="text-gray-600 mt-1">Kelola parameter Genetic Algorithm untuk penataan paket</p>
                <p class="text-xs text-gray-400 mt-1">Hanya 1 parameter yang dapat aktif dalam satu waktu</p>
            </div>
            <button onclick="openModal()" class="bg-pos-red text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">
                Tambah Parameter
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Population</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Generation</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Crossover Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mutation Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Utilisasi Volume</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Packing</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($parameters as $param)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $param->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($param->population_size) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($param->generation_limit) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ ($param->crossover_rate * 100) }}%</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ ($param->mutation_rate * 100) }}%</td>
                        <td class="px-6 py-4">
                            @if($param->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($param->packings_count > 0)
                                <span class="px-2 py-1 text-xs rounded-full {{ $param->avg_volume_utilization >= 80 ? 'bg-green-100 text-green-800' : ($param->avg_volume_utilization >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ number_format($param->avg_volume_utilization, 1) }}%
                                </span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $param->packings_count }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if(!$param->is_active)
                            <form action="{{ route('admin.ga-parameters.activate', $param->id) }}" method="POST" class="inline activate-form" data-param-name="{{ $param->name }}">
                                @csrf
                                @method('PATCH')
                                <button type="button" class="activate-btn px-3 py-1 text-sm rounded bg-green-100 text-green-600 hover:bg-green-200 transition">Aktifkan</button>
                            </form>
                            @else
                            <span class="text-gray-400 text-sm">(Aktif)</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $parameters->links() }}
        </div>
    </div>
</div>

{{-- Modal Tambah Parameter --}}
<div id="paramModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Parameter GA</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form id="paramForm" action="{{ route('admin.ga-parameters.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Parameter *</label>
                    <input type="text" name="name" id="name" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Population Size *</label>
                        <input type="number" name="population_size" id="population_size" required min="10" max="500" value="100" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Generation Limit *</label>
                        <input type="number" name="generation_limit" id="generation_limit" required min="10" max="1000" value="150" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Crossover Rate *</label>
                        <input type="number" step="0.01" name="crossover_rate" id="crossover_rate" required min="0" max="1" value="0.8" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mutation Rate *</label>
                        <input type="number" step="0.01" name="mutation_rate" id="mutation_rate" required min="0" max="1" value="0.2" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red">
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">Batal</button>
                <button type="submit" class="px-4 py-2 bg-pos-red text-white rounded-lg hover:bg-red-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

@include('components.modal-confirm')

<script>
    const modal = document.getElementById('paramModal');
    
    function openModal() {
        modal.classList.remove('hidden');
        document.getElementById('paramForm').reset();
        document.getElementById('population_size').value = 100;
        document.getElementById('generation_limit').value = 150;
        document.getElementById('crossover_rate').value = 0.8;
        document.getElementById('mutation_rate').value = 0.2;
    }
    
    function closeModal() {
        modal.classList.add('hidden');
    }
    
    // Konfirmasi aktivasi parameter
    document.querySelectorAll('.activate-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.activate-form');
            const paramName = form.dataset.paramName;
            
            showConfirmModal(
                'Konfirmasi Aktivasi Parameter',
                `Apakah Anda yakin ingin mengaktifkan parameter "${paramName}"? Parameter lain yang aktif akan otomatis dinonaktifkan.`,
                () => form.submit()
            );
        });
    });
    
    // Tutup modal jika klik di luar
    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    }
</script>
@endsection