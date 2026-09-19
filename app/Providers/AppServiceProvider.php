<?php

namespace App\Providers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Observers\TransaksiObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Observers\DetailTransaksiObserver;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https');

        Vite::prefetch(concurrency: 3);
        setlocale(LC_TIME, 'id_ID.utf8');
        Carbon::setLocale('id');

        // Sinkronisasi otomatis rekap shodaqah ke Google Sheets
        DetailTransaksi::observe(DetailTransaksiObserver::class);
        Transaksi::observe(TransaksiObserver::class);
    }
}
