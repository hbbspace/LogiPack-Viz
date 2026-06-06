@extends('layouts.admin')

@section('title', 'Container Management - Admin')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Container Management</h1>
                <p class="text-gray-600 mt-1">Kelola container yang tersedia untuk penataan</p>
            </div>
            <button onclick="openModal()" class="bg-pos-red text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">
                Tambah Container
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dimensi (PxLxT)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Volume Max</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Berat Max</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($containers as $container)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $container->name }}</td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-500">{{ $container->type_code ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $container->length }} x {{ $container->width }} x {{ $container->height }} cm</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($container->volume_max) }} cm³</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($container->weight_max, 2) }} kg</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $container->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $container->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <form action="{{ route('admin.containers.toggle', $container->id) }}" method="POST" class="inline toggle-form" data-container-name="{{ $container->name }}" data-container-status="{{ $container->is_active ? 'nonaktifkan' : 'aktifkan' }}">
                                @csrf
                                @method('PATCH')
                                <button type="button" class="toggle-btn px-3 py-1 text-sm rounded {{ $container->is_active ? 'bg-gray-100 text-gray-600 hover:bg-gray-200' : 'bg-green-100 text-green-600 hover:bg-green-200' }} transition">
                                    {{ $container->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $containers->links() }}
        </div>
    </div>
</div>

{{-- Modal Tambah Container --}}
<div id="containerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Tambah Container</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form id="containerForm" action="{{ route('admin.containers.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Container *</label>
                    <input type="text" name="name" id="name" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type Code</label>
                    <input type="text" name="type_code" id="type_code" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Panjang (cm) *</label>
                        <input type="number" step="0.01" name="length" id="length" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lebar (cm) *</label>
                        <input type="number" step="0.01" name="width" id="width" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tinggi (cm) *</label>
                        <input type="number" step="0.01" name="height" id="height" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Berat Maksimal (kg) *</label>
                    <input type="number" step="0.01" name="weight_max" id="weight_max" required class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" id="description" rows="2" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red"></textarea>
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
    const modal = document.getElementById('containerModal');
    
    function openModal() {
        modal.classList.remove('hidden');
        document.getElementById('containerForm').reset();
    }
    
    function closeModal() {
        modal.classList.add('hidden');
    }
    
    // Konfirmasi toggle container
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('.toggle-form');
            const containerName = form.dataset.containerName;
            const action = form.dataset.containerStatus;
            
            showConfirmModal(
                'Konfirmasi ' + (action === 'aktifkan' ? 'Aktivasi' : 'Nonaktifkan'),
                `Apakah Anda yakin ingin ${action} container "${containerName}"?`,
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