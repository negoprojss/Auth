<?php

use App\Livewire\AdminDashboard;
use App\Livewire\UsuarioDashboard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::get('/admin/dashboard', AdminDashboard::class)
        ->middleware('role:admin')
        ->name('admin.dashboard');

    Route::get('/usuario/dashboard', UsuarioDashboard::class)
        ->middleware('role:usuario')
        ->name('usuario.dashboard');

});

require __DIR__.'/settings.php';
