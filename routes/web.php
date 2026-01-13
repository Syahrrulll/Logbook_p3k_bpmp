<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogbookController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function() {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [LogbookController::class, 'dashboard'])->name('dashboard');

    // Logbook (Input & History)
    Route::get('/logbook/input', [LogbookController::class, 'create'])->name('logbook.create');
    Route::post('/logbook/store', [LogbookController::class, 'store'])->name('logbook.store');
    Route::get('/logbook/riwayat', [LogbookController::class, 'history'])->name('logbook.history');

    // === FITUR EDIT & DELETE LOGBOOK ===
    Route::get('/logbook/{id}/edit', [LogbookController::class, 'edit'])->name('logbook.edit');
    Route::put('/logbook/{id}', [LogbookController::class, 'update'])->name('logbook.update');
    Route::delete('/logbook/{id}', [LogbookController::class, 'destroy'])->name('logbook.destroy');

    // === FITUR EDIT PROFIL ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Admin
    Route::get('/admin/monitoring', [LogbookController::class, 'adminMonitoring'])->name('admin.monitoring');
    Route::get('/admin/monitoring/export', [LogbookController::class, 'exportLogbooks'])->name('admin.export');
    Route::get('/admin/monitoring/print', [LogbookController::class, 'printLogbooks'])->name('admin.print');
});
