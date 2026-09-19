<?php

namespace App\Services;

use Throwable;
use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Google\Service\Sheets\ValueRange;
use Google\Service\Sheets\AddSheetRequest;
use Google\Service\Sheets\ClearValuesRequest;
use Google\Service\Sheets\BatchUpdateSpreadsheetRequest;

class GoogleSheetsService
{
    /**
     * Mencegah sinkronisasi lebih dari satu kali dalam satu request
     * (satu aksi admin bisa mengubah banyak baris detail_transaksi).
     */
    protected static bool $syncedThisRequest = false;

    protected const SCOPES = [Sheets::SPREADSHEETS];

    /**
     * Sinkronkan rekap shodaqah jamaah ke Google Sheets.
     *
     * Formatnya sama dengan halaman /admin/rekap-shodaqah:
     * satu baris per jamaah berisi total persenan, jimpitan, dapur pusat,
     * shodaqah daerah, shodaqah kelompok, jumlah, dan status.
     *
     * @return bool true jika sinkronisasi berhasil (atau sudah dilakukan di request ini)
     */
    public function syncRekapShodaqah(bool $force = false): bool
    {
        if (! $force && self::$syncedThisRequest) {
            return true;
        }

        $spreadsheetId = config('google-sheets.spreadsheet_id');
        $sheetName = config('google-sheets.sheet_name');

        if (! $spreadsheetId || ! $sheetName) {
            Log::debug('GoogleSheetsService: GOOGLE_SHEETS_SPREADSHEET_ID belum dikonfigurasi, sinkronisasi dilewati.');
            self::$syncedThisRequest = true;
            return false;
        }

        $sheets = $this->client();
        if (! $sheets) {
            Log::warning('GoogleSheetsService: kredensial Google Sheets tidak ditemukan di ' . config('google-sheets.credentials_path') . '. Pastikan GOOGLE_SHEETS_CREDENTIALS di .env sudah benar.');
            self::$syncedThisRequest = true;
            return false;
        }

        try {
            $this->ensureSheetExists($sheets, $spreadsheetId, $sheetName);

            $rows = $this->buildRekapRows();
            $range = "'" . $sheetName . "'!A1";

            // Kosongkan sheet dulu, lalu tulis ulang seluruh data rekap
            $sheets->spreadsheets_values->clear($spreadsheetId, $range, new ClearValuesRequest());

            if (! empty($rows)) {
                $body = new ValueRange(['values' => $rows]);
                $sheets->spreadsheets_values->update($spreadsheetId, $range, $body, ['valueInputOption' => 'RAW']);
            }

            self::$syncedThisRequest = true;
            return true;
        } catch (Throwable $e) {
            Log::error('GoogleSheetsService: gagal sinkronisasi rekap shodaqah ke Google Sheets: ' . $e->getMessage(), [
                'spreadsheet_id' => $spreadsheetId,
                'sheet_name' => $sheetName,
            ]);
            return false;
        }
    }

    /**
     * Buat koneksi Google Sheets API menggunakan kredensial service account.
     */
    protected function client(): ?Sheets
    {
        $credentialsPath = config('google-sheets.credentials_path');

        if (! $credentialsPath || ! file_exists($credentialsPath)) {
            return null;
        }

        $client = new Client();
        $client->setApplicationName(config('google-sheets.application_name'));
        $client->setScopes(self::SCOPES);
        $client->setAuthConfig($credentialsPath);

        return new Sheets($client);
    }

    /**
     * Pastikan tab/sheet tujuan ada di spreadsheet, buat otomatis jika belum ada.
     */
    protected function ensureSheetExists(Sheets $sheets, string $spreadsheetId, string $sheetName): void
    {
        $spreadsheet = $sheets->spreadsheets->get($spreadsheetId);

        foreach ($spreadsheet->getSheets() as $sheet) {
            if ($sheet->getProperties()->getTitle() === $sheetName) {
                return;
            }
        }

        $batch = new BatchUpdateSpreadsheetRequest([
            'requests' => [
                ['addSheet' => new AddSheetRequest(['properties' => ['title' => $sheetName]])],
            ],
        ]);

        $sheets->spreadsheets->batchUpdate($spreadsheetId, $batch);
    }

    /**
     * Susun baris rekap shodaqah, sama dengan query halaman /admin/rekap-shodaqah
     * (tanpa filter tanggal agar spreadsheet menjadi cerminan lengkap data).
     */
    protected function buildRekapRows(): array
    {
        $data = DB::table('jamaah')
            ->leftJoin(DB::raw('(
                SELECT
                    detail_transaksi.jamaah_id,
                    MAX(transaksi.tanggal) as tanggal,
                    SUM(detail_transaksi.persenan) as persenan,
                    SUM(detail_transaksi.jimpitan) as jimpitan,
                    SUM(detail_transaksi.dapur_pusat) as dapur_pusat,
                    SUM(detail_transaksi.shodaqah_daerah) as shodaqah_daerah,
                    SUM(detail_transaksi.shodaqah_kelompok) as shodaqah_kelompok,
                    SUM(detail_transaksi.jumlah) as jumlah,
                    COUNT(transaksi.id) as transaksi_count
                FROM detail_transaksi
                INNER JOIN transaksi ON detail_transaksi.transaksi_id = transaksi.id
                WHERE transaksi.jenis = "pemasukan"
                GROUP BY detail_transaksi.jamaah_id
            ) as dt'), function ($join) {
                $join->on('jamaah.id', '=', 'dt.jamaah_id');
            })
            ->select(
                'jamaah.id as jamaah_id',
                'jamaah.nama',
                'dt.tanggal',
                DB::raw('COALESCE(dt.persenan, 0) as persenan'),
                DB::raw('COALESCE(dt.jimpitan, 0) as jimpitan'),
                DB::raw('COALESCE(dt.dapur_pusat, 0) as dapur_pusat'),
                DB::raw('COALESCE(dt.shodaqah_daerah, 0) as shodaqah_daerah'),
                DB::raw('COALESCE(dt.shodaqah_kelompok, 0) as shodaqah_kelompok'),
                DB::raw('COALESCE(dt.jumlah, 0) as jumlah'),
                DB::raw('CASE WHEN dt.transaksi_count > 0 THEN "Sudah Setor" ELSE "Belum Setor" END as status')
            )
            ->orderBy('jamaah.id', 'asc')
            ->get();

        $rows = [[
            'No',
            'Nama Jamaah',
            'Tanggal',
            'Persenan',
            'Jimpitan',
            'Dapur Pusat',
            'Shodaqah Daerah',
            'Shodaqah Kelompok',
            'Jumlah',
            'Status',
        ]];

        foreach ($data as $index => $row) {
            $rows[] = [
                $index + 1,
                $row->nama,
                $row->tanggal ?? '',
                (float) $row->persenan,
                (float) $row->jimpitan,
                (float) $row->dapur_pusat,
                (float) $row->shodaqah_daerah,
                (float) $row->shodaqah_kelompok,
                (float) $row->jumlah,
                $row->status,
            ];
        }

        return $rows;
    }
}