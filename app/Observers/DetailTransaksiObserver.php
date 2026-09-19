<?php

namespace App\Observers;

use App\Models\DetailTransaksi;
use App\Services\GoogleSheetsService;
use Illuminate\Support\Facades\DB;

class DetailTransaksiObserver
{
    /**
     * Sinkronkan rekap shodaqah ke Google Sheets setiap ada perubahan
     * data detail transaksi (tambah / edit / hapus).
     */
    public function created(DetailTransaksi $detailTransaksi): void
    {
        $this->scheduleSync();
    }

    public function updated(DetailTransaksi $detailTransaksi): void
    {
        $this->scheduleSync();
    }

    public function deleted(DetailTransaksi $detailTransaksi): void
    {
        $this->scheduleSync();
    }

    /**
     * Jalankan setelah transaksi database di-commit agar sheet tidak
     * tersinkron dengan data yang ternyata di-rollback.
     */
    protected function scheduleSync(): void
    {
        DB::afterCommit(fn () => app(GoogleSheetsService::class)->syncRekapShodaqah());
    }
}