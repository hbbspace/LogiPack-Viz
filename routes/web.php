<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PackingController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ContainerController;
use App\Http\Controllers\Admin\GaParameterController;
use App\Http\Controllers\Admin\PackingController as AdminPackingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/upload', [UploadController::class, 'index'])->name('upload.index');
    Route::get('/upload/template', [UploadController::class, 'downloadTemplate'])->name('upload.template');
    Route::post('/upload', [UploadController::class, 'upload'])->name('upload.store');
    Route::get('/upload/{id}', [UploadController::class, 'show'])->name('upload.show');
    Route::delete('/upload/{id}', [UploadController::class, 'destroy'])->name('upload.destroy');
    Route::get('/packing', [PackingController::class, 'index'])->name('packing.index');
    Route::post('/packing/process', [PackingController::class, 'process'])->name('packing.process');
    Route::get('/packing/result/{id}', [PackingController::class, 'result'])->name('packing.result');
    Route::get('/packing/result/{id}/visualization', [PackingController::class, 'visualization'])->name('packing.visualization');
    Route::get('/packing/history', [PackingController::class, 'history'])->name('packing.history');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{id}/toggle', [UserController::class, 'toggle'])->name('users.toggle');

    Route::get('/containers', [ContainerController::class, 'index'])->name('containers.index');
    Route::post('/containers', [ContainerController::class, 'store'])->name('containers.store');
    Route::patch('/containers/{id}/toggle', [ContainerController::class, 'toggle'])->name('containers.toggle');

    Route::get('/ga-parameters', [GaParameterController::class, 'index'])->name('ga-parameters.index');
    Route::post('/ga-parameters', [GaParameterController::class, 'store'])->name('ga-parameters.store');
    Route::patch('/ga-parameters/{id}/activate', [GaParameterController::class, 'activate'])->name('ga-parameters.activate');
    
    Route::get('/packings', [AdminPackingController::class, 'index'])->name('packings.index');
});

// Redirect root
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin() 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('dashboard');
    }
    return redirect()->route('login');
});