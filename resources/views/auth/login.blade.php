@extends('layouts.app')

@section('title', 'Login - PosLogistik')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background: linear-gradient(135deg, #FF0000 0%, #0066B3 100%);">
    <div class="max-w-md w-full space-y-8 bg-white rounded-2xl shadow-2xl p-8">
        <div>
            <div class="flex justify-center">
                <div class="w-20 h-20 bg-pos-red rounded-full flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-3xl">P</span>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Pos<span class="text-pos-red">Logistik</span>
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Sistem Penataan Paket 3D
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only">Username</label>
                    <input id="username" name="username" type="text" required 
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-pos-red focus:border-pos-red focus:z-10 sm:text-sm" 
                        placeholder="Username" value="{{ old('username') }}">
                </div>
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required 
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-pos-red focus:border-pos-red focus:z-10 sm:text-sm" 
                        placeholder="Password">
                </div>
            </div>

            @error('username')
                <div class="text-red-600 text-sm text-center">{{ $message }}</div>
            @enderror

            <div>
                <button type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-pos-red hover-bg-pos-red focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pos-red transition">
                    Masuk
                </button>
            </div>
        </form>
        
        <div class="text-center text-xs text-gray-500">
            <p>Demo Account:</p>
            <p>Admin: admin / admin123</p>
            <p>User SUB01: user_sub01 / user123</p>
        </div>
    </div>
</div>
@endsection