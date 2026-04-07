<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PosLogistik - Sistem Penataan Paket 3D')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        .bg-pos-red { background-color: #FF0000; }
        .bg-pos-blue { background-color: #0066B3; }
        .text-pos-red { color: #FF0000; }
        .text-pos-blue { color: #0066B3; }
        .border-pos-red { border-color: #FF0000; }
        .hover-bg-pos-red:hover { background-color: #CC0000; }
        .hover-bg-pos-blue:hover { background-color: #004C8C; }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-100">
    @auth
    <nav class="bg-white shadow-lg border-b-4 border-pos-red">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-8">
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-pos-red rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-xl">P</span>
                        </div>
                        <span class="font-bold text-xl text-gray-800">Pos<span class="text-pos-red">Logistik</span></span>
                    </div>
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-pos-red px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <a href="{{ route('packing.index') }}" class="text-gray-700 hover:text-pos-red px-3 py-2 rounded-md text-sm font-medium">Penataan Paket</a>
                        <a href="{{ route('packing.history') }}" class="text-gray-700 hover:text-pos-red px-3 py-2 rounded-md text-sm font-medium">Riwayat</a>
                        @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-pos-red px-3 py-2 rounded-md text-sm font-medium">Admin Panel</a>
                        @endif
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <div class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-500">{{ auth()->user()->branch->city ?? 'Admin' }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-pos-red text-white px-4 py-2 rounded-lg text-sm font-medium hover-bg-pos-red transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>