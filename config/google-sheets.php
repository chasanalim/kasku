<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nama Aplikasi
    |--------------------------------------------------------------------------
    |
    | Nama aplikasi yang dilaporkan ke Google API (opsional).
    |
    */

    'application_name' => env('GOOGLE_SHEETS_APPLICATION_NAME', env('APP_NAME', 'Laravel')),

    /*
    |--------------------------------------------------------------------------
    | Kredensial Service Account
    |--------------------------------------------------------------------------
    |
    | Path ke file JSON kredensial service account yang diunduh dari
    | Google Cloud Console. Simpan file di storage/app/google/credentials.json
    | lalu isi path-nya di .env (GOOGLE_SHEETS_CREDENTIALS).
    |
    */

    'credentials_path' => env('GOOGLE_SHEETS_CREDENTIALS', ''),

    /*
    |--------------------------------------------------------------------------
    | ID Spreadsheet
    |--------------------------------------------------------------------------
    |
    | ID spreadsheet Google Sheets tujuan. Bisa dilihat dari URL spreadsheet,
    | contoh: https://docs.google.com/spreadsheets/d/ABCDEF123456/edit
    | maka ID-nya adalah ABCDEF123456.
    |
    */

    'spreadsheet_id' => env('GOOGLE_SHEETS_SPREADSHEET_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Nama Sheet / Tab
    |--------------------------------------------------------------------------
    |
    | Nama tab di dalam spreadsheet tempat data rekap ditulis. Jika tab belum
    | ada, akan dibuat otomatis saat sinkronisasi pertama.
    |
    */

    'sheet_name' => env('GOOGLE_SHEETS_SHEET_NAME', 'RekapShodaqah'),
];