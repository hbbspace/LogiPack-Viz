<?php

namespace App\Http\Controllers;

use App\Models\BatchImport;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $batchImports = BatchImport::where('user_id', $user->id)
            ->withCount('packages')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('upload.index', compact('batchImports'));
    }
    
    public function downloadTemplate()
    {
        $templatePath = public_path('templates/template_packages.csv');
        
        if (!file_exists($templatePath)) {
            // Buat template default jika file belum ada
            $headers = ['id', 'length', 'width', 'height', 'weight'];
            $callback = function() use ($headers) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $headers);
                fputcsv($file, ['P001', 30, 20, 15, 5.5]);
                fputcsv($file, ['P002', 50, 40, 30, 15.2]);
                fputcsv($file, ['P003', 100, 80, 60, 45.0]);
                fclose($file);
            };
            
            return response()->stream($callback, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="template_packages.csv"',
            ]);
        }
        
        return response()->download($templatePath, 'template_packages.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
    
    public function upload(Request $request)
    {   
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);
        
        $file = $request->file('csv_file');
        $originalName = $file->getClientOriginalName();
        
        // Parse CSV untuk validasi awal
        $csvData = array_map('str_getcsv', file($file->getPathname()));
        
        if (count($csvData) < 2) {
            return redirect()->route('upload.index')
                ->with('error', 'File CSV tidak memiliki data (minimal 1 baris data setelah header).');
        }
        
        $header = array_shift($csvData);
        
        // Validasi header dan mapping kolom
        $indices = $this->mapColumns($header);
        
        if (!$indices['length'] || !$indices['width'] || !$indices['height'] || !$indices['weight']) {
            return redirect()->route('upload.index')
                ->with('error', 'CSV harus memiliki kolom: length, width, height, weight');
        }
        
        // Validasi setiap baris
        $validRows = 0;
        $invalidRows = [];
        
        foreach ($csvData as $rowIndex => $row) {
            $length = floatval($row[$indices['length']]);
            $width = floatval($row[$indices['width']]);
            $height = floatval($row[$indices['height']]);
            $weight = floatval($row[$indices['weight']]);
            $id = $indices['id'] !== null ? trim($row[$indices['id']]) : null;
            
            if ($length > 0 && $width > 0 && $height > 0 && $weight > 0 && !empty($id)) {
                $validRows++;
            } else {
                $invalidRows[] = $rowIndex + 2; // +2 karena header di baris 1
            }
        }
        
        if ($validRows === 0) {
            return redirect()->route('upload.index')
                ->with('error', 'Tidak ada data valid dalam CSV. Pastikan setiap baris memiliki id, length, width, height, weight yang positif.');
        }
        
        // Simpan file dan proses hanya data valid
        $filePath = $file->store('batch_imports', 'public');
        
        DB::beginTransaction();
        
        try {
            $batchImport = BatchImport::create([
                'file_path' => $filePath,
                'original_name' => $originalName,
                'total_packages' => $validRows,
                'user_id' => Auth::id(),
            ]);
            
            $user = Auth::user();
            $packagesCreated = 0;
            
            foreach ($csvData as $rowIndex => $row) {
                $length = floatval($row[$indices['length']]);
                $width = floatval($row[$indices['width']]);
                $height = floatval($row[$indices['height']]);
                $weight = floatval($row[$indices['weight']]);
                $id = $indices['id'] !== null ? trim($row[$indices['id']]) : null;
                
                if ($length <= 0 || $width <= 0 || $height <= 0 || $weight <= 0 || empty($id)) {
                    continue; // Skip baris tidak valid
                }
                
                Package::create([
                    'tracking_number' => $id,
                    'length' => $length,
                    'width' => $width,
                    'height' => $height,
                    'weight' => $weight,
                    'volume' => $length * $width * $height,
                    'status' => 'pending',
                    'notes' => null,
                    'batch_import_id' => $batchImport->id,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
                $packagesCreated++;
            }
            
            DB::commit();
            
            $message = "Berhasil upload {$packagesCreated} paket";
            if (!empty($invalidRows)) {
                $message .= ". Baris tidak valid: " . implode(', ', $invalidRows);
            }
            
            return redirect()->route('upload.index')->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollback();
            Storage::disk('public')->delete($filePath);
            Log::error('Upload CSV Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    private function mapColumns($header)
    {
        $headerLower = array_map('strtolower', $header);
        
        $idIdx = array_search('id', $headerLower);
        $lengthIdx = array_search('length', $headerLower);
        $widthIdx = array_search('width', $headerLower);
        $heightIdx = array_search('height', $headerLower);
        $weightIdx = array_search('weight', $headerLower);
        
        return [
            'id' => $idIdx !== false ? $idIdx : null,
            'length' => $lengthIdx !== false ? $lengthIdx : null,
            'width' => $widthIdx !== false ? $widthIdx : null,
            'height' => $heightIdx !== false ? $heightIdx : null,
            'weight' => $weightIdx !== false ? $weightIdx : null,
        ];
    }
    
    public function show($id)
    {
        $batchImport = BatchImport::with('packages')->findOrFail($id);
        
        // Pastikan user hanya bisa lihat miliknya sendiri
        if ($batchImport->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses ke batch ini.');
        }
        
        return view('upload.show', compact('batchImport'));
    }
    
    public function destroy($id)
    {
        $batchImport = BatchImport::findOrFail($id);
        
        // Pastikan user hanya bisa hapus miliknya sendiri
        if ($batchImport->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus batch ini.');
        }

        // Cek apakah ada package yang sudah dipacking
        $hasPackedPackages = $batchImport->packages()
            ->where('status', 'packed')
            ->exists();
        
        if ($hasPackedPackages) {
            return back()->with('error', 'Batch ini tidak bisa dihapus karena sudah ada paket yang diproses dalam penataan.');
        }
        
        // Hapus file CSV
        Storage::disk('public')->delete($batchImport->file_path);
        
        // Hapus semua package (akan cascade karena foreign key)
        $batchImport->delete();
        
        return redirect()->route('upload.index')->with('success', 'Batch import berhasil dihapus');
    }
}