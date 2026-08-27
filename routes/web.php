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

    Route::livewire(
            '/users',
            'admin.users.index'
        )->name('admin.users.index');

    Route::livewire(
            '/users/create',
            'admin.users.create'
        )->name('admin.users.create');

    Route::livewire(
            '/users/{user}/edit',
            'admin.users.edit'
        )->name('admin.users.edit');

});

require __DIR__.'/settings.php';
