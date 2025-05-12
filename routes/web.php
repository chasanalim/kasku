<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\IRController;
use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\JamaahController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\PrivilegesController;


Route::prefix('admin')->as('admin.')->middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard/Dashboard');
    })->name('dashboard');
    Route::resource('user', UserAdminController::class);
    Route::resource('privileges', PrivilegesController::class);

    //BARU
    Route::resource('jamaah', JamaahController::class);
    Route::resource('akun', AkunController::class);
    Route::resource('transaksi', TransaksiController::class);
    Route::resource('ir', IRController::class);
    Route::resource('laporan', LaporanController::class);
});

require __DIR__ . '/auth.php';
