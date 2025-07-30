<?php

namespace Database\Seeders;

use App\Models\AkunRekening;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AkunRekeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akunRekening = [
            ['nama' => 'KAS KELOMPOK', 'kode_akun' => '101', 'tipe' => 'Kas', 'saldo_awal' => 0],
            ['nama' => 'DANA ASAD', 'kode_akun' => '102', 'tipe' => 'Kas', 'saldo_awal' => 0],
            ['nama' => 'LISTRIK', 'kode_akun' => '103', 'tipe' => 'Kas', 'saldo_awal' => 0],
            ['nama' => 'DANA SOSIAL', 'kode_akun' => '104', 'tipe' => 'Kas', 'saldo_awal' => 0],
            ['nama' => 'JATAH PROKER', 'kode_akun' => '105', 'tipe' => 'Kas', 'saldo_awal' => 0],
            ['nama' => 'SISA DAPUR', 'kode_akun' => '106', 'tipe' => 'Kas', 'saldo_awal' => 0],
            ['nama' => 'KAS MT', 'kode_akun' => '107', 'tipe' => 'Kas', 'saldo_awal' => 0],
            ['nama' => 'KAS TEKS', 'kode_akun' => '108', 'tipe' => 'Kas', 'saldo_awal' => 0],
            ['nama' => 'KAS IBU-IBU', 'kode_akun' => '109', 'tipe' => 'Kas', 'saldo_awal' => 0],
            ['nama' => 'KAS ARISAN', 'kode_akun' => '110', 'tipe' => 'Kas', 'saldo_awal' => 0],
            ['nama' => 'KAS MT TPQ', 'kode_akun' => '111', 'tipe' => 'Kas', 'saldo_awal' => 0],
            ['nama' => 'KAS CABERAWIT', 'kode_akun' => '112', 'tipe' => 'Kas', 'saldo_awal' => 0],
            ['nama' => 'KAS AGHNIYA', 'kode_akun' => '113', 'tipe' => 'Kas', 'saldo_awal' => 0],

            ['nama' => 'INFAQ PENGAJIAN', 'kode_akun' => '401', 'tipe' => 'Pemasukan', 'saldo_awal' => 0],
            ['nama' => 'INFAQ JUMATAN', 'kode_akun' => '402', 'tipe' => 'Pemasukan', 'saldo_awal' => 0],
            ['nama' => 'SHODAQOH DANA SOSIAL', 'kode_akun' => '403', 'tipe' => 'Pemasukan', 'saldo_awal' => 0],
            ['nama' => 'SHODAQOH ASAD', 'kode_akun' => '404', 'tipe' => 'Pemasukan', 'saldo_awal' => 0],
            ['nama' => 'SHODAQOH LISTRIK', 'kode_akun' => '405', 'tipe' => 'Pemasukan', 'saldo_awal' => 0],
            ['nama' => 'SHODAQOH DAPUR MINI+PUSAT', 'kode_akun' => '406', 'tipe' => 'Pemasukan', 'saldo_awal' => 0],
            ['nama' => 'SHODAQOH PPG', 'kode_akun' => '407', 'tipe' => 'Pemasukan', 'saldo_awal' => 0],
            ['nama' => 'SHODAQOH MT DAERAH', 'kode_akun' => '408', 'tipe' => 'Pemasukan', 'saldo_awal' => 0],
            ['nama' => 'SHODAQOH KK', 'kode_akun' => '409', 'tipe' => 'Pemasukan', 'saldo_awal' => 0],

            ['nama' => 'BEBAN LISTRIK', 'kode_akun' => '501', 'tipe' => 'Pengeluaran', 'saldo_awal' => 0],
            ['nama' => 'KONSUMSI', 'kode_akun' => '502', 'tipe' => 'Pengeluaran', 'saldo_awal' => 0],
            ['nama' => 'ASAD', 'kode_akun' => '503', 'tipe' => 'Pengeluaran', 'saldo_awal' => 0],
            ['nama' => 'TAKZIYAH', 'kode_akun' => '504', 'tipe' => 'Pengeluaran', 'saldo_awal' => 0],

            ['nama' => 'IR', 'kode_akun' => '601', 'tipe' => 'Penampung', 'saldo_awal' => 0],
            ['nama' => 'DAPUR PUSAT', 'kode_akun' => '602', 'tipe' => 'Penampung', 'saldo_awal' => 0],
            ['nama' => 'SHODAQAOH DAERAH', 'kode_akun' => '603', 'tipe' => 'Penampung', 'saldo_awal' => 0],
            ['nama' => 'TABUNGAN MASJID DAERAH', 'kode_akun' => '604', 'tipe' => 'Penampung', 'saldo_awal' => 0],
        ];

        AkunRekening::insert($akunRekening);
    }
}
