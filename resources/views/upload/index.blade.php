@extends('layouts.app')

@section('title', 'Upload CSV - PosLogistik')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Upload Batch Paket</h1>
                <p class="text-gray-600 mt-1">Upload file CSV berisi daftar paket yang akan ditata</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('upload.template') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-600 transition">
                    📥 Download Template CSV
                </a>
            </div>
        </div>
    </div>

    {{-- Form Upload --}}
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-pos-red transition">
                <div class="text-5xl mb-3">📁</div>
                <p class="text-gray-600 mb-2">Klik atau drag & drop file CSV di sini</p>
                <p class="text-xs text-gray-400">Format: CSV dengan kolom id, length, width, height, weight</p>
                <input type="file" name="csv_file" id="csv_file" class="hidden" accept=".csv,.txt">
                <button type="button" id="selectFileBtn" class="mt-4 bg-pos-red text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">
                    Pilih File
                </button>
                <span id="fileName" class="ml-3 text-sm text-gray-500"></span>
            </div>
            @error('csv_file')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
            <button type="submit" id="submitBtn" class="mt-6 w-full bg-pos-blue text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition" style="display: none;">
                Upload & Proses
            </button>
        </form>
    </div>

    {{-- Riwayat Upload --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Riwayat Upload</h2>
        </div>
        
        @if($batchImports->count() > 0)
        <div class="overflow-x-auto shadow overflow-y-auto border-b border-gray-200 sm:rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Total Paket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Pending</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Packed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($batchImports as $batch)
                    @php
                        $pendingCount = $batch->packages->where('status', 'pending')->count();
                        $packedCount = $batch->packages->where('status', 'packed')->count();
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $batch->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $batch->original_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">{{ $batch->total_packages }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ $pendingCount > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ $pendingCount }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                {{ $packedCount }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2 whitespace-nowrap">
                            <a href="{{ route('upload.show', $batch->id) }}" class="px-3 py-1 text-sm rounded bg-blue-100 text-blue-600 hover:bg-blue-200 transition">Detail</a>
                            <form action="{{ route('upload.destroy', $batch->id) }}" method="POST" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="px-3 py-1 text-sm rounded bg-red-100 text-red-600 hover:bg-red-200 transition delete-btn">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12 text-gray-500">
            <div class="text-5xl mb-3">📂</div>
            <p>Belum ada batch import</p>
            <p class="text-sm">Upload file CSV untuk memulai</p>
        </div>
        @endif
    </div>
</div>
@endsection

@include('components.modal-confirm')

@section('scripts')
<script>
    const fileInput = document.getElementById('csv_file');
    const selectBtn = document.getElementById('selectFileBtn');
    const fileNameSpan = document.getElementById('fileName');
    const submitBtn = document.getElementById('submitBtn');

    selectBtn.addEventListener('click', () => fileInput.click());
    
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileNameSpan.textContent = this.files[0].name;
            submitBtn.style.display = 'block';
        } else {
            fileNameSpan.textContent = '';
            submitBtn.style.display = 'none';
        }
    });
    
    // Konfirmasi hapus
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('form');

            showConfirmModal(
                'Konfirmasi Hapus Batch',
                `Apakah anda yakin ingin menghapus batch ini? Semua paket di dalamnya juga akan terhapus.`,
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

    submitBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (!fileInput.files.length) {
            alert('Pilih file CSV terlebih dahulu');
            return;
        }
        
        // Baca file untuk preview dan validasi
        const file = fileInput.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const content = e.target.result;
            const lines = content.split('\n');
            const headers = lines[0].split(',');
            
            // Validasi header
            const requiredHeaders = ['length', 'width', 'height', 'weight'];
            const hasId = headers.map(h => h.toLowerCase().trim()).includes('id');
            const missingHeaders = requiredHeaders.filter(h => 
                !headers.map(hh => hh.toLowerCase().trim()).includes(h)
            );
            
            // if (missingHeaders.length > 0) {
            //     alert(`Header CSV harus memiliki kolom: ${missingHeaders.join(', ')}`);
            //     return;
            // }
            
            // Hitung jumlah baris valid
            let validCount = 0;
            let sampleData = [];
            
            for (let i = 1; i < Math.min(lines.length, 6); i++) {
                if (lines[i].trim()) {
                    const cols = lines[i].split(',');
                    if (cols.length >= 5) {
                        sampleData.push(cols);
                    }
                }
            }
            
            // Hitung total valid rows
            for (let i = 1; i < lines.length; i++) {
                if (lines[i].trim()) {
                    const cols = lines[i].split(',');
                    const length = parseFloat(cols[headers.findIndex(h => h.toLowerCase().trim() === 'length')]);
                    const width = parseFloat(cols[headers.findIndex(h => h.toLowerCase().trim() === 'width')]);
                    const height = parseFloat(cols[headers.findIndex(h => h.toLowerCase().trim() === 'height')]);
                    const weight = parseFloat(cols[headers.findIndex(h => h.toLowerCase().trim() === 'weight')]);
                    
                    if (length > 0 && width > 0 && height > 0 && weight > 0) {
                        validCount++;
                    }
                }
            }
            
            // if (validCount === 0) {
            //     alert('Tidak ada data valid dalam CSV. Pastikan setiap baris memiliki dimensi dan berat yang positif.');
            //     return;
            // }
            
            // Tampilkan modal konfirmasi
            showConfirmModal(
                'Konfirmasi Upload',
                `Akan mengupload ${validCount} paket. Lanjutkan?`,
                () => document.getElementById('uploadForm').submit()
            );
        };
        
        reader.readAsText(file);
    });
</script>
@endsection