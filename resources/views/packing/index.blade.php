@extends('layouts.app')

@section('title', 'Penataan Paket - PosLogistik')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800">Penataan Paket ke Container</h1>
        <p class="text-gray-600 mt-1">Pilih container dan paket yang akan ditata</p>
    </div>

    <form method="POST" action="{{ route('packing.process') }}" id="packingForm">
        @csrf
        
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-pos-red text-white rounded-full flex items-center justify-center text-sm mr-2">1</span>
                Pilih Container
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($containers as $container)
                <label class="border-2 rounded-lg p-4 cursor-pointer transition hover:border-pos-red {{ old('container_id') == $container->id ? 'border-pos-red bg-red-50' : 'border-gray-200' }}">
                    <input type="radio" name="container_id" value="{{ $container->id }}" class="hidden" required>
                    <div class="text-center">
                        <div class="text-3xl mb-2">🚚</div>
                        <div class="font-semibold text-gray-800">{{ $container->name }}</div>
                        <div class="text-sm text-gray-500">{{ $container->length }}x{{ $container->width }}x{{ $container->height }} cm</div>
                        <div class="text-sm text-gray-500">Max: {{ $container->weight_max }} kg</div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('container_id')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-pos-red text-white rounded-full flex items-center justify-center text-sm mr-2">2</span>
                Pilih Paket
            </h2>
            
            @if($packages->count() > 0)
            <div class="mb-3">
                <button type="button" id="selectAll" class="text-pos-blue text-sm hover:underline">Pilih Semua</button>
                <button type="button" id="deselectAll" class="text-gray-500 text-sm hover:underline ml-3">Hapus Semua</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-96 overflow-y-auto">
                @foreach($packages as $package)
                <label class="border rounded-lg p-3 cursor-pointer hover:bg-gray-50 transition">
                    <input type="checkbox" name="package_ids[]" value="{{ $package->id }}" class="package-checkbox mr-2">
                    <div class="inline-block">
                        <div class="font-medium text-gray-800">{{ $package->tracking_number }}</div>
                        <div class="text-xs text-gray-500">{{ $package->length }}x{{ $package->width }}x{{ $package->height }} cm, {{ $package->weight }} kg</div>
                        <div class="text-xs text-gray-400">
                            Tujuan: {{ $package->branchDestination->city ?? ($package->branch_destination_id ? 'ID:' . $package->branch_destination_id : '-') }}
                        </div>
                    </div>
                </label>
                @endforeach
            </div>
            <p class="text-sm text-gray-500 mt-3" id="selectedCount">0 paket dipilih</p>
            @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-5xl mb-3">📦</div>
                <p>Tidak ada paket yang tersedia untuk penataan.</p>
                <a href="#" class="text-pos-blue hover:underline mt-2 inline-block">Input Paket Baru</a>
            </div>
            @endif
        </div>

        @if($packages->count() > 0)
        <div class="flex justify-end space-x-3">
            <button type="submit" class="bg-pos-red text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700 transition">
                Proses Penataan
            </button>
        </div>
        @endif
    </form>
</div>
@endsection

@section('scripts')
<script>
    const checkboxes = document.querySelectorAll('.package-checkbox');
    const selectedCount = document.getElementById('selectedCount');
    
    function updateCount() {
        const checked = document.querySelectorAll('.package-checkbox:checked').length;
        if (selectedCount) {
            selectedCount.textContent = checked + ' paket dipilih';
        }
    }
    
    const selectAllBtn = document.getElementById('selectAll');
    const deselectAllBtn = document.getElementById('deselectAll');
    
    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = true);
            updateCount();
        });
    }
    
    if (deselectAllBtn) {
        deselectAllBtn.addEventListener('click', () => {
            checkboxes.forEach(cb => cb.checked = false);
            updateCount();
        });
    }
    
    checkboxes.forEach(cb => cb.addEventListener('change', updateCount));
    updateCount();
    
    // Container selection visual
    document.querySelectorAll('input[name="container_id"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="container_id"]').forEach(r => {
                const label = r.closest('label');
                if (label) {
                    label.classList.remove('border-pos-red', 'bg-red-50');
                }
            });
            if(this.checked) {
                const label = this.closest('label');
                if (label) {
                    label.classList.add('border-pos-red', 'bg-red-50');
                }
            }
        });
    });
</script>
@endsection