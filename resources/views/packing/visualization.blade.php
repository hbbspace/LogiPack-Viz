<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Visualisasi - {{ $packing->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #1a1a1a;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            height: 100vh;
            overflow: hidden;
        }
        
        .visualization-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        
        .toolbar {
            background: #2d2d2d;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #3d3d3d;
            flex-shrink: 0;
            z-index: 10;
        }
        
        .toolbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .toolbar-title {
            color: #fff;
            font-size: 16px;
            font-weight: 600;
        }
        
        .toolbar-subtitle {
            color: #888;
            font-size: 13px;
        }
        
        .toolbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .btn-back {
            background: #4a4a4a;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-back:hover {
            background: #5a5a5a;
        }
        
        .btn-back svg {
            width: 18px;
            height: 18px;
        }
        
        .viz-frame {
            flex: 1;
            width: 100%;
            border: none;
            background: #1a1a1a;
        }
        
        .error-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: #1a1a1a;
        }
        
        .error-icon {
            font-size: 64px;
            margin-bottom: 16px;
        }
        
        .error-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .error-message {
            color: #888;
            font-size: 16px;
        }
        
        @media (max-width: 768px) {
            .toolbar {
                flex-direction: column;
                gap: 8px;
                padding: 12px 16px;
            }
            
            .toolbar-left {
                flex-direction: column;
                gap: 4px;
                text-align: center;
            }
            
            .toolbar-title {
                font-size: 14px;
            }
            
            .toolbar-subtitle {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="visualization-container">
        {{-- Toolbar --}}
        <div class="toolbar">
            <div class="toolbar-left">
                <div>
                    <div class="toolbar-title">Detail Visualisasi 3D</div>
                    <div class="toolbar-subtitle">{{ $packing->name }} • {{ $packing->placedPackages->count() }} paket termuat</div>
                </div>
            </div>
            <div class="toolbar-right">
                <span style="color: #666; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                    <span style="display: inline-block; width: 8px; height: 8px; background: #4CAF50; border-radius: 50%;"></span>
                    Interaktif
                </span>
                <a href="{{ route('packing.result', $packing->id) }}" class="btn-back">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Hasil
                </a>
            </div>
        </div>
        
        {{-- Visualisasi --}}
        @if($fileExists)
            @php
                $vizPathFull = $packing->visualization_file_path;
                if (str_starts_with($vizPathFull, '/')) {
                    $vizPathFull = substr($vizPathFull, 1);
                }
            @endphp
            <iframe src="{{ asset($vizPathFull) }}" class="viz-frame" title="Detail Visualisasi 3D"></iframe>
        @else
            <div class="error-container">
                <div class="error-icon">📦</div>
                <div class="error-title">File Visualisasi Tidak Ditemukan</div>
                <div class="error-message">Maaf, file visualisasi detail tidak tersedia untuk penataan ini.</div>
            </div>
        @endif
    </div>
</body>
</html>