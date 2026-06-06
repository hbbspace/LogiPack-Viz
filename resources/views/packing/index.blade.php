@extends('layouts.app')

@section('title', 'Penataan Paket - PosLogistik')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-800">Penataan Paket ke Container</h1>
        <p class="text-gray-600 mt-1">Pilih batch import, container, dan paket yang akan ditata</p>
        @if(isset($activeGaParam))
        <p class="text-xs text-gray-400 mt-2">Parameter GA aktif: {{ $activeGaParam->name }} (Populasi: {{ $activeGaParam->population_size }}, Generasi: {{ $activeGaParam->generation_limit }})</p>
        @endif
    </div>

    <form method="POST" action="{{ route('packing.process') }}" id="packingForm">
        @csrf
        
        {{-- Step 1: Pilih Batch Import --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-pos-red text-white rounded-full flex items-center justify-center text-sm mr-2">1</span>
                Pilih Batch Import
            </h2>
            
            @if($batchImports->count() > 0)
            <select name="batch_import_id" id="batchSelect" class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pos-red" required>
                <option value="">-- Pilih Batch Import --</option>
                @foreach($batchImports as $batch)
                <option value="{{ $batch->id }}" data-packages='@json($batch->packages)'>
                    {{ $batch->original_name }} ({{ $batch->packages->count() }} paket pending) - {{ $batch->created_at->format('d/m/Y H:i') }}
                </option>
                @endforeach
            </select>
            @else
            <div class="text-center py-8 text-gray-500">
                <div class="text-5xl mb-3">📦</div>
                <p>Tidak ada batch import dengan paket pending.</p>
                <br>
                <a href="{{ route('upload.index') }}" class="px-3 py-1 text-sm rounded bg-blue-100 text-blue-600 hover:bg-blue-200 transition">Upload CSV →</a>
            </div>
            @endif
        </div>

        {{-- Step 2: Pilih Container --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6" id="containerSection" style="display: none;">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-pos-red text-white rounded-full flex items-center justify-center text-sm mr-2">2</span>
                Pilih Container
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($containers as $container)
                <label class="border-2 rounded-lg p-4 cursor-pointer transition hover:border-pos-red container-option">
                    <input type="radio" name="container_id" value="{{ $container->id }}" class="hidden container-radio" required>
                    <div class="text-center">
                        <div class="text-3xl mb-2">🚚</div>
                        <div class="font-semibold text-gray-800">{{ $container->name }}</div>
                        <div class="text-sm text-gray-500">{{ $container->length }}x{{ $container->width }}x{{ $container->height }} cm</div>
                        <div class="text-sm text-gray-500">Max: {{ number_format($container->weight_max, 2) }} kg</div>
                    </div>
                </label>
                @endforeach
            </div>
            @error('container_id')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
        </div>

        {{-- Step 3: Pilih Paket --}}
        <div class="bg-white rounded-lg shadow p-6 mb-6" id="packagesSection" style="display: none;">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-pos-red text-white rounded-full flex items-center justify-center text-sm mr-2">3</span>
                Pilih Paket
            </h2>
            
            <div class="mb-3">
                <button type="button" id="selectAll" class="px-3 py-1 text-sm rounded bg-blue-100 text-blue-600 hover:bg-blue-200">Pilih Semua</button>
                <button type="button" id="deselectAll" class="px-3 py-1 text-sm rounded bg-gray-100 text-gray-600 hover:bg-gray-200">Hapus Semua</button>
            </div>
            
            <div id="packagesList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-96 overflow-y-auto">
                {{-- Akan diisi dengan JavaScript --}}
            </div>
            
            <p class="text-sm text-gray-500 mt-3" id="selectedCount">0 paket dipilih</p>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end" id="submitSection" style="display: none;">
            <button type="button" id="submitBtn" class="bg-pos-red text-white px-6 py-2 rounded-lg font-medium hover:bg-red-700 transition">
                Proses Penataan
            </button>
        </div>
    </form>
</div>
@endsection

@include('components.modal-confirm')

@section('scripts')
<script>
    const batchSelect = document.getElementById('batchSelect');
    const containerSection = document.getElementById('containerSection');
    const packagesSection = document.getElementById('packagesSection');
    const submitSection = document.getElementById('submitSection');
    const packagesList = document.getElementById('packagesList');
    const selectedCountSpan = document.getElementById('selectedCount');
    const containerRadios = document.querySelectorAll('.container-radio');
    
    let currentPackages = [];
    let activeGaParam = null;
    
    // Ambil parameter GA dari server
    @if(isset($activeGaParam))
    activeGaParam = {
        population_size: {{ $activeGaParam->population_size }},
        generation_limit: {{ $activeGaParam->generation_limit }}
    };
    @endif
    
    // Fungsi estimasi waktu berdasarkan parameter GA
    function estimateTime(populationSize, generations, numPackages) {
        // Rumus estimasi berdasarkan data aktual:
        // - 50 pop, 50 gen, 9 pkg → 25 detik
        // - 100 pop, 150 gen, 9 pkg → 95 detik
        // - 100 pop, 150 gen, 39 pkg → 600 detik (10 menit)
        
        let baseTime = (populationSize * generations * Math.sqrt(numPackages)) / 100;
        
        // Kalibrasi berdasarkan jumlah paket
        if (numPackages <= 10) {
            baseTime = baseTime * 0.4;  // 9 paket → lebih cepat
        } else if (numPackages >= 30) {
            baseTime = baseTime * 1.1;  // 39 paket → lebih lambat
        } else if (numPackages >= 20) {
            baseTime = baseTime * 0.7;  // 20-30 paket → sedang
        }
        
        // Batas minimal dan maksimal (15 detik - 30 menit)
        baseTime = Math.max(15, Math.min(baseTime, 1800));
        
        return Math.round(baseTime);
    }
    
    function formatTime(seconds) {
        if (seconds < 60) return `${seconds} detik`;
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        if (secs === 0) return `${minutes} menit`;
        return `${minutes} menit ${secs} detik`;
    }
    
    // Simpan packages dari batch yang dipilih
    batchSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value && selectedOption.dataset.packages) {
            currentPackages = JSON.parse(selectedOption.dataset.packages);
            renderPackagesList(currentPackages);
            containerSection.style.display = 'block';
            packagesSection.style.display = 'block';
        } else {
            containerSection.style.display = 'none';
            packagesSection.style.display = 'none';
            submitSection.style.display = 'none';
            currentPackages = [];
        }
        
        // Reset container selection
        containerRadios.forEach(radio => {
            radio.checked = false;
            const label = radio.closest('.container-option');
            if (label) {
                label.classList.remove('border-pos-red', 'bg-red-50');
            }
        });
        
        updateSubmitVisibility();
    });
    
    function renderPackagesList(packages) {
        if (!packagesList) return;
        
        if (packages.length === 0) {
            packagesList.innerHTML = '<div class="col-span-full text-center py-8 text-gray-500">Tidak ada paket pending dalam batch ini</div>';
            return;
        }
        
        packagesList.innerHTML = packages.map(pkg => `
            <label class="border rounded-lg p-3 cursor-pointer hover:bg-gray-50 transition package-label">
                <input type="checkbox" name="package_ids[]" value="${pkg.id}" class="package-checkbox mr-2">
                <div class="inline-block">
                    <div class="font-medium text-gray-800">${pkg.tracking_number}</div>
                    <div class="text-xs text-gray-500">${parseFloat(pkg.length).toFixed(1)}x${parseFloat(pkg.width).toFixed(1)}x${parseFloat(pkg.height).toFixed(1)} cm, ${parseFloat(pkg.weight).toFixed(2)} kg</div>
                    <div class="text-xs text-gray-400">Volume: ${(pkg.length * pkg.width * pkg.height).toLocaleString()} cm³</div>
                </div>
            </label>
        `).join('');
        
        attachCheckboxEvents();
    }
    
    function attachCheckboxEvents() {
        const checkboxes = document.querySelectorAll('.package-checkbox');
        const selectAllBtn = document.getElementById('selectAll');
        const deselectAllBtn = document.getElementById('deselectAll');
        
        function updateCount() {
            const checked = document.querySelectorAll('.package-checkbox:checked').length;
            if (selectedCountSpan) {
                selectedCountSpan.textContent = checked + ' paket dipilih';
            }
            updateSubmitVisibility();
        }
        
        if (selectAllBtn) {
            selectAllBtn.onclick = () => {
                checkboxes.forEach(cb => cb.checked = true);
                updateCount();
            };
        }
        
        if (deselectAllBtn) {
            deselectAllBtn.onclick = () => {
                checkboxes.forEach(cb => cb.checked = false);
                updateCount();
            };
        }
        
        checkboxes.forEach(cb => {
            cb.removeEventListener('change', updateCount);
            cb.addEventListener('change', updateCount);
        });
        
        updateCount();
    }
    
    function updateSubmitVisibility() {
        const containerSelected = document.querySelector('input[name="container_id"]:checked') !== null;
        const packagesSelected = document.querySelectorAll('.package-checkbox:checked').length > 0;
        
        submitSection.style.display = (containerSelected && packagesSelected) ? 'flex' : 'none';
    }
    
    // Container selection visual
    document.querySelectorAll('.container-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.container-option').forEach(label => {
                label.classList.remove('border-pos-red', 'bg-red-50');
            });
            if (this.checked) {
                const label = this.closest('.container-option');
                if (label) {
                    label.classList.add('border-pos-red', 'bg-red-50');
                }
            }
            updateSubmitVisibility();
        });
    });
    
    // Submit button with loading modal
    document.getElementById('submitBtn').addEventListener('click', function() {
        const containerSelected = document.querySelector('input[name="container_id"]:checked');
        const packagesSelected = document.querySelectorAll('.package-checkbox:checked');
        
        if (!containerSelected) {
            alert('Pilih container terlebih dahulu');
            return;
        }
        
        if (packagesSelected.length === 0) {
            alert('Pilih minimal satu paket');
            return;
        }

        // Tampilkan modal konfirmasi
        showConfirmModal(
            'Konfirmasi Penataan',
            `Anda akan memproses ${packagesSelected.length} paket. Proses ini mungkin memakan waktu beberapa menit. Lanjutkan?`,
            () => startPackingProcess(containerSelected, packagesSelected)
        );
    });
        
    async function startPackingProcess(containerSelected, packagesSelected) {
         // Hitung estimasi waktu
        let estimatedSeconds = 30; // default
        if (activeGaParam) {
            estimatedSeconds = estimateTime(
                activeGaParam.population_size,
                activeGaParam.generation_limit,
                packagesSelected.length
            );
        }

        // Tampilkan modal loading
        showLoadingModal(estimatedSeconds, packagesSelected.length);
        
        // Kumpulkan data form
        const formData = new FormData();
        formData.append('container_id', containerSelected.value);
        packagesSelected.forEach(cb => {
            formData.append('package_ids[]', cb.value);
        });
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        
        let startTime = Date.now();
        let timerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const timerEl = document.getElementById('timerInfo');
            if (timerEl) timerEl.textContent = `Waktu: ${elapsed} detik`;
            
            // Update progress bar berdasarkan waktu aktual vs estimasi
            const progressBar = document.getElementById('progressBar');
            if (progressBar && estimatedSeconds > 0) {
                let progress = Math.min(95, (elapsed / estimatedSeconds) * 100);
                progressBar.style.width = `${progress}%`;
            }
        }, 1000);
        
        try {
            const response = await fetch('{{ route("packing.process") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            clearInterval(timerInterval);
            
            if (response.redirected) {
                // Redirect sukses
                window.location.href = response.url;
            } else {
                const result = await response.json();
                if (result.success && result.redirect_url) {
                    window.location.href = result.redirect_url;
                } else {
                    hideLoadingModal();
                    alert('Error: ' + (result.error || 'Terjadi kesalahan'));
                }
            }
        } catch (error) {
            clearInterval(timerInterval);
            hideLoadingModal();
            alert('Gagal mengirim request: ' + error.message);
        }
    }
    
    function showLoadingModal(estimatedSeconds, numPackages) {
        const estimatedText = formatTime(estimatedSeconds);
        const modalHtml = `
            <div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl p-8 max-w-md w-full mx-4">
                    <div class="text-center">
                        <div class="loader mx-auto mb-4"></div>
                        <h2 class="text-xl font-semibold text-gray-800 mb-2">Sedang Memproses Penataan</h2>
                        <p class="text-gray-600 mb-2">Menjalankan algoritma genetika...</p>
                        <p class="text-sm text-gray-500 mb-1">Jumlah paket: ${numPackages}</p>
                        <p class="text-sm text-gray-500 mb-3">Estimasi waktu: ~${estimatedText}</p>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                            <div id="progressBar" class="bg-pos-red h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <p class="text-xs text-gray-400" id="timerInfo">Waktu: 0 detik</p>
                        <p class="text-xs text-gray-400 mt-2">Mohon tunggu, jangan tutup halaman ini</p>
                    </div>
                </div>
            </div>
            <style>
                .loader {
                    width: 48px;
                    height: 48px;
                    border: 5px solid #E5E7EB;
                    border-bottom-color: #FF0000;
                    border-radius: 50%;
                    display: inline-block;
                    box-sizing: border-box;
                    animation: rotation 1s linear infinite;
                }
                @keyframes rotation {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }
    
    function hideLoadingModal() {
        const modal = document.getElementById('loadingModal');
        if (modal) modal.remove();
    }
</script>
@endsection