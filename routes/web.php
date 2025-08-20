<?php

use Inertia\Inertia;
use App\Models\MasterTabungan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Admin\InfaqController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\JamaahController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\ShodaqahController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PemasukanController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\PrivilegesController;
use App\Http\Controllers\Admin\KasKelompokController;
use App\Http\Controllers\Admin\PengeluaranController;
use App\Http\Controllers\Admin\MasterTabunganController;
use App\Http\Controllers\Admin\TabunganMasjidController;

Route::prefix('admin')->as('admin.')->middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route::get('/dashboard', function () {
    //     return Inertia::render('Admin/Dashboard/Dashboard');
    // })->name('dashboard');

    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::resource('user', UserAdminController::class);
    Route::resource('privileges', PrivilegesController::class);

    //BARU
    Route::resource('jamaah', JamaahController::class);
    Route::resource('mastertabungan', MasterTabunganController::class);
    Route::resource('akun', AkunController::class);
    Route::resource('transaksi', TransaksiController::class);
    Route::resource('shodaqah', ShodaqahController::class);
    Route::post('shodaqah/verify/{id}', [ShodaqahController::class, 'verify'])->name('shodaqah.verify');
    Route::post('shodaqah/verify-multiple', [ShodaqahController::class, 'verifyMultiple'])->name('shodaqah.verify-multiple');
    Route::delete('shodaqah/destroy-multiple', [ShodaqahController::class, 'destroyMultiple'])->name('shodaqah.destroy-multiple');

    Route::resource('infaq', InfaqController::class);
    Route::post('infaq/verify/{id}', [InfaqController::class, 'verify'])->name('infaq.verify');
    Route::post('infaq/verify-multiple', [InfaqController::class, 'verifyMultiple'])->name('infaq.verify-multiple');
    Route::delete('infaq/destroy-multiple', [InfaqController::class, 'destroyMultiple'])->name('infaq.destroy-multiple');

    Route::get('rekap-shodaqah', [LaporanController::class, 'rekapShodaqah'])->name('laporan.rekap-shodaqah');
    Route::get('rekap-shodaqah-desa', [LaporanController::class, 'rekapShodaqahDesa'])->name('laporan.rekap-shodaqah-desa');
    Route::get('laporan', [LaporanController::class, 'laporan'])->name('laporan');
    Route::get('buku-besar', [LaporanController::class, 'bukuBesar'])->name('buku-besar');
    Route::get('jurnal', [LaporanController::class, 'jurnal'])->name('jurnal');
    Route::get('rekap-tabungan-masjid', [LaporanController::class, 'rekapTabungan'])->name('laporan.rekap-tabungan');
    Route::get('laporan/tabungan-detail', [LaporanController::class, 'getTabunganDetail'])
        ->name('laporan.tabungan-detail');

    Route::resource('pengeluaran', PengeluaranController::class);
    Route::post('pengeluaran/verify/{id}', [PengeluaranController::class, 'verify'])->name('pengeluaran.verify');
    Route::post('pengeluaran/verify-multiple', [PengeluaranController::class, 'verifyMultiple'])->name('pengeluaran.verify-multiple');
    Route::delete('pengeluaran/destroy-multiple', [PengeluaranController::class, 'destroyMultiple'])->name('pengeluaran.destroy-multiple');

    Route::resource('pemasukan', PemasukanController::class);
    Route::post('pemasukan/verify/{id}', [PemasukanController::class, 'verify'])->name('pemasukan.verify');
    Route::post('pemasukan/verify-multiple', [PemasukanController::class, 'verifyMultiple'])->name('pemasukan.verify-multiple');
    Route::delete('pemasukan/destroy-multiple', [PemasukanController::class, 'destroyMultiple'])->name('pemasukan.destroy-multiple');

    Route::resource('kaskelompok', KasKelompokController::class);
    Route::post('kaskelompok/verify/{id}', [KasKelompokController::class, 'verify'])->name('kaskelompok.verify');
    Route::post('kaskelompok/verify-multiple', [KasKelompokController::class, 'verifyMultiple'])->name('kaskelompok.verify-multiple');
    Route::delete('kaskelompok/destroy-multiple', [KasKelompokController::class, 'destroyMultiple'])->name('kaskelompok.destroy-multiple');

    Route::get('shodaqah/export/pdf', [ExportController::class, 'exportPDF'])->name('shodaqah.export.pdf');
    Route::get('shodaqah/export/excel', [ExportController::class, 'exportExcel'])->name('shodaqah.export.excel');
    Route::get('shodaqah-desa/export/pdf', [ExportController::class, 'exportPDFDesa'])->name('shodaqah-desa.export.pdf');
    Route::get('shodaqah-desa/export/excel', [ExportController::class, 'exportExcelDesa'])->name('shodaqah-desa.export.excel');

    Route::get('laporan/export/pdf', [ExportController::class, 'exportPDFlaporan'])->name('laporan.export.pdf');

    Route::resource('tabungan', TabunganMasjidController::class);
    Route::post('tabungan/verify/{id}', [TabunganMasjidController::class, 'verify'])->name('tabungan.verify');
    Route::post('tabungan/verify-multiple', [TabunganMasjidController::class, 'verifyMultiple'])->name('tabungan.verify-multiple');
    Route::delete('tabungan/destroy-multiple', [TabunganMasjidController::class, 'destroyMultiple'])->name('tabungan.destroy-multiple');
});

require __DIR__ . '/auth.php';
