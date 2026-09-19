<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\GoogleSheetsService;

class SyncRekapShodaqah extends Command
{
    protected $signature = 'rekap-shodaqah:sync';

    protected $description = 'Sinkronkan rekap shodaqah jamaah ke Google Sheets secara manual';

    public function handle(GoogleSheetsService $service): int
    {
        if ($service->syncRekapShodaqah(force: true)) {
            $this->info('Rekap shodaqah berhasil disinkronkan ke Google Sheets.');

            return self::SUCCESS;
        }

        $this->error('Sinkronisasi gagal atau belum dikonfigurasi. Periksa GOOGLE_SHEETS_* di .env dan log aplikasi.');

        return self::FAILURE;
    }
}