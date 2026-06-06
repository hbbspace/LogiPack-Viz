<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUser
{
    public function handle(Request $request, Closure $next)
    {
        // Jika admin, redirect ke admin dashboard
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Admin tidak memiliki akses ke halaman user.');
        }
        
        return $next($request);
    }
}