<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // return response()->json([
        return Inertia::render('Admin/Dashboard/Dashboard', [
            // Banmod Data
            'kas_kelompok' => [
                'summary' => $this->getKasKelompokSummary(),
                'pemasukan' => $this->getPemasukanBulanan(),
                'pengeluaran' => $this->getPengeluaranBulanan(),
            ],
            'tabungan' => [
                'summary' => $this->getTabunganSummary(),
                'uang_masuk' => $this->getMasukBulanan(),
                'uang_keluar' => $this->getKeluarBulanan(),
            ],

        ]);
    }

    // Banmod Methods
    private function getKasKelompokSummary()
    {
        $bulan = now()->month;
        $tahun = now()->year;

        // Bulan & tahun sebelumnya
        $bulan_lalu = $bulan == 1 ? 12 : $bulan - 1;
        $tahun_lalu = $bulan == 1 ? $tahun - 1 : $tahun;

        // Format tanggal awal & akhir bulan sekarang
        $tanggal_awal = date("$tahun-$bulan-01");
        $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));

        // Format tanggal awal & akhir bulan lalu
        $tanggal_awal_lalu = date("$tahun_lalu-$bulan_lalu-01");
        $tanggal_akhir_lalu = date("Y-m-t", strtotime($tanggal_awal_lalu));

        // Get saldo kas terkini (all time)
        $saldoPemasukan = DB::table('transaksi')
            ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
            ->where('transaksi.jenis', 'pemasukan_kas')
            ->select(DB::raw('
            COALESCE(SUM(detail_transaksi.jumlah), 0) as total_pemasukan
        '))
            ->first();

        $saldoPengeluaran = DB::table('transaksi')
            ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
            ->where('transaksi.jenis', 'pengeluaran_kas')
            ->select(DB::raw('
            COALESCE(SUM(detail_transaksi.jumlah), 0) as total_pengeluaran
        '))
            ->first();

        $saldo_kas = $saldoPemasukan->total_pemasukan - $saldoPengeluaran->total_pengeluaran;

        // Get pemasukan bulan ini
        $pemasukan_bulan_ini = DB::table('transaksi as t')
            ->join('detail_transaksi as dt', 't.id', '=', 'dt.transaksi_id')
            ->where('t.jenis', 'pemasukan_kas')
            ->whereBetween('t.tanggal', [$tanggal_awal, $tanggal_akhir])
            ->sum('dt.jumlah');

        // Get pengeluaran bulan ini
        $pengeluaran_bulan_ini = DB::table('transaksi as t')
            ->join('detail_transaksi as dt', 't.id', '=', 'dt.transaksi_id')
            ->where('t.jenis', 'pengeluaran_kas')
            ->whereBetween('t.tanggal', [$tanggal_awal, $tanggal_akhir])
            ->sum('dt.jumlah');

        // Get pemasukan bulan lalu
        $pemasukan_bulan_lalu = DB::table('transaksi as t')
            ->join('detail_transaksi as dt', 't.id', '=', 'dt.transaksi_id')
            ->where('t.jenis', 'pemasukan_kas')
            ->whereBetween('t.tanggal', [$tanggal_awal_lalu, $tanggal_akhir_lalu])
            ->sum('dt.jumlah');

        // Get pengeluaran bulan lalu
        $pengeluaran_bulan_lalu = DB::table('transaksi as t')
            ->join('detail_transaksi as dt', 't.id', '=', 'dt.transaksi_id')
            ->where('t.jenis', 'pengeluaran_kas')
            ->whereBetween('t.tanggal', [$tanggal_awal_lalu, $tanggal_akhir_lalu])
            ->sum('dt.jumlah');

        // Hitung saldo bulan lalu
        $saldo_kas_bulan_lalu = $pemasukan_bulan_lalu - $pengeluaran_bulan_lalu;

        // Fungsi hitung persentase perubahan
        $hitungPersentase = function ($sekarang, $lalu) {
            if ($lalu == 0) {
                return $sekarang > 0 ? 100 : 0;
            }
            return (($sekarang - $lalu) / $lalu) * 100;
        };

        return [
            'saldo_kas' => $saldo_kas,
            'persentase_saldo' => $hitungPersentase($saldo_kas, $saldo_kas_bulan_lalu),

            'pemasukan_bulan_ini' => $pemasukan_bulan_ini,
            'persentase_pemasukan' => $hitungPersentase($pemasukan_bulan_ini, $pemasukan_bulan_lalu),

            'pengeluaran_bulan_ini' => $pengeluaran_bulan_ini,
            'persentase_pengeluaran' => $hitungPersentase($pengeluaran_bulan_ini, $pengeluaran_bulan_lalu),
        ];
    }


    private function getPemasukanBulanan()
    {

        // Get data for last 12 months including current month
        $pemasukan = DB::table('transaksi as t')
            ->join('detail_transaksi as dt', 't.id', '=', 'dt.transaksi_id')
            ->select(
                DB::raw('MONTH(t.tanggal) as bulan'),
                DB::raw('YEAR(t.tanggal) as tahun'),
                DB::raw('SUM(dt.jumlah) as total')
            )
            ->where('t.jenis', 'pemasukan_kas')
            ->where('dt.jumlah', '>', 0)
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->map(function ($item) {
                return [
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'total' => $item->total,
                    'nama_bulan' => date('F', mktime(0, 0, 0, $item->bulan, 1))
                ];
            });

        return $pemasukan;
    }

    private function getPengeluaranBulanan()
    {

        // Get data for last 12 months including current month
        $pengeluaran = DB::table('transaksi as t')
            ->join('detail_transaksi as dt', 't.id', '=', 'dt.transaksi_id')
            ->select(
                DB::raw('MONTH(t.tanggal) as bulan'),
                DB::raw('YEAR(t.tanggal) as tahun'),
                DB::raw('SUM(dt.jumlah) as total')
            )
            ->where('t.jenis', 'pengeluaran_kas')
            ->where('dt.jumlah', '>', 0)
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->map(function ($item) {
                return [
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'total' => $item->total,
                    'nama_bulan' => date('F', mktime(0, 0, 0, $item->bulan, 1))
                ];
            });

        return $pengeluaran;
    }

    private function getTabunganSummary()
    {
        $total_jatah = DB::table('master_tabungan')->sum('jatah');

        $uang_masuk = DB::table('tabungan_masjid')->sum('jumlah');

        $uang_keluar = DB::table('transaksi as t')
            ->join('detail_transaksi as dt', 't.id', '=', 'dt.transaksi_id')
            ->join('akun_rekening as ar', 't.akun_id', '=', 'ar.id')
            ->where('t.jenis', 'pengeluaran')
            ->where('ar.kode_akun', '604')
            ->sum('dt.jumlah');

        $uang_belum_setor = DB::table('akun_rekening')->where('kode_akun', '604')->sum('saldo_awal');
        // Fungsi hitung persentase perubahan
        $hitungPersentase = function ($sekarang, $lalu) {
            if ($lalu == 0) {
                return $sekarang > 0 ? 100 : 0;
            }
            return ($sekarang / $lalu) * 100;
        };
        return [
            'total_jatah' => $total_jatah,

            'uang_masuk' => $uang_masuk,
            'persentase_uang_masuk' => $hitungPersentase($uang_masuk, $total_jatah),

            'uang_keluar' => $uang_keluar,
            'persentase_uang_keluar' => $hitungPersentase($uang_keluar, $total_jatah),

            'uang_belum_setor' => $uang_belum_setor,
        ];
    }


    private function getMasukBulanan()
    {

        // Get data for last 12 months including current month
        $uang_masuk = DB::table('tabungan_masjid as t')
            ->select(
                DB::raw('MONTH(t.tanggal) as bulan'),
                DB::raw('YEAR(t.tanggal) as tahun'),
                DB::raw('SUM(t.jumlah) as total')
            )
            ->where('t.jumlah', '>', 0)
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->map(function ($item) {
                return [
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'total' => $item->total,
                    'nama_bulan' => date('F', mktime(0, 0, 0, $item->bulan, 1))
                ];
            });

        return $uang_masuk;
    }

    private function getKeluarBulanan()
    {

        // Get data for last 12 months including current month
        $pengeluaran = DB::table('transaksi as t')
            ->join('detail_transaksi as dt', 't.id', '=', 'dt.transaksi_id')
            ->join('akun_rekening as ar', 't.akun_id', '=', 'ar.id')
            ->select(
                DB::raw('MONTH(t.tanggal) as bulan'),
                DB::raw('YEAR(t.tanggal) as tahun'),
                DB::raw('SUM(dt.jumlah) as total')
            )
            ->where('t.jenis', 'pengeluaran')
            ->where('ar.kode_akun', '604')
            ->where('dt.jumlah', '>', 0)
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->map(function ($item) {
                return [
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'total' => $item->total,
                    'nama_bulan' => date('F', mktime(0, 0, 0, $item->bulan, 1))
                ];
            });

        return $pengeluaran;
    }
}
