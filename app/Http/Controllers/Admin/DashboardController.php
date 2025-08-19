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

        ]);
    }

    // Banmod Methods
    private function getKasKelompokSummary()
    {
        $bulan = now()->month;
        $tahun = now()->year;

        // Format tanggal awal & akhir bulan
        $tanggal_awal = date("$tahun-$bulan-01");
        $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));

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

        return [
            'saldo_kas' => $saldoPemasukan->total_pemasukan - $saldoPengeluaran->total_pengeluaran,
            'pemasukan_bulan_ini' => $pemasukan_bulan_ini,
            'pengeluaran_bulan_ini' => $pengeluaran_bulan_ini
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
}
