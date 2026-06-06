<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('branch')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.users.index', compact('users'));
    }
    
    public function toggle($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent admin from disabling themselves
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }
        
        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "User {$user->name} berhasil {$status}.");
    }
}