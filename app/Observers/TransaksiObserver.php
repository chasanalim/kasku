<?php

namespace App\Observers;

use App\Models\Transaksi;
use App\Services\GoogleSheetsService;
use Illuminate\Support\Facades\DB;

class TransaksiObserver
{
    /**
     * Saat transaksi dihapus, baris detail_transaksi ikut terhapus lewat
     * onDelete('cascade') di database sehingga event Eloquent DetailTransaksi
     * tidak terpicu. Sinkronkan ulang di sini agar sheet tetap akurat.
     */
    public function deleted(Transaksi $transaksi): void
    {
        DB::afterCommit(fn () => app(GoogleSheetsService::class)->syncRekapShodaqah());
    }
}