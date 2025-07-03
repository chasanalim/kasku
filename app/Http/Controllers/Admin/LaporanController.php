<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\DetailTransaksi;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class LaporanController extends Controller
{
    public function rekapShodaqah(Request $request)
    {
        if ($request->wantsJson()) {
            // Parse tanggal dari request
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;

            $data = DB::table('jamaah')
                ->leftJoin('detail_transaksi', 'jamaah.id', '=', 'detail_transaksi.jamaah_id')
                ->leftJoin('transaksi', function ($join) use ($tanggalAwal, $tanggalAkhir) {
                    $join->on('detail_transaksi.transaksi_id', '=', 'transaksi.id')
                        ->where('transaksi.jenis', '=', 'pemasukan');

                    if ($tanggalAwal && $tanggalAkhir) {
                        $join->whereBetween('transaksi.tanggal', [$tanggalAwal, $tanggalAkhir]);
                    }
                })
                ->select(
                    'jamaah.id as jamaah_id',
                    'jamaah.nama',
                    DB::raw('MAX(transaksi.tanggal) as tanggal'),
                    DB::raw('COALESCE(SUM(detail_transaksi.persenan), 0) as persenan'),
                    DB::raw('COALESCE(SUM(detail_transaksi.jimpitan), 0) as jimpitan'),
                    DB::raw('COALESCE(SUM(detail_transaksi.dapur_pusat), 0) as dapur_pusat'),
                    DB::raw('COALESCE(SUM(detail_transaksi.shodaqah_daerah), 0) as shodaqah_daerah'),
                    DB::raw('COALESCE(SUM(detail_transaksi.shodaqah_kelompok), 0) as shodaqah_kelompok'),
                    DB::raw('COALESCE(SUM(detail_transaksi.jumlah), 0) as jumlah'),
                    DB::raw('CASE WHEN COUNT(transaksi.id) > 0 THEN "Sudah Setor" ELSE "Belum Setor" END as status')
                )
                ->groupBy('jamaah.id', 'jamaah.nama')
                ->orderBy('jamaah.id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status_format', function ($row) {
                    $class = $row->status == 'Sudah Setor' ? 'success' : 'danger';
                    return "<span class='badge bg-{$class}'>{$row->status}</span>";
                })
                ->rawColumns(['status_format'])
                ->make(true);
        }

        return Inertia::render('Admin/Laporan/RekapShodaqah', [
            'title' => 'Data Shodaqah Jamaah',
            'flash' => ['message' => session('message')]
        ]);
    }

    public function laporan(Request $request)
{
    if ($request->wantsJson()) {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // Format tanggal awal & akhir bulan
        $tanggal_awal = date("$tahun-$bulan-01");
        $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));

        // Saldo akhir bulan lalu
        $bulan_lalu = date('m', strtotime('-1 month', strtotime($tanggal_awal)));
        $tahun_lalu = date('Y', strtotime('-1 month', strtotime($tanggal_awal)));
        $tanggal_akhir_bulan_lalu = date("Y-m-t", strtotime("$tahun_lalu-$bulan_lalu-01"));

        // Hitung saldo akhir bulan lalu
        $total_pemasukan_lalu = DB::table('transaksi')
            ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
            ->where('transaksi.jenis', 'pemasukan_kas')
            ->where('transaksi.tanggal', '<=', $tanggal_akhir_bulan_lalu)
            ->sum('detail_transaksi.jumlah');

        $total_pengeluaran_lalu = DB::table('transaksi')
            ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
            ->where('transaksi.jenis', 'pengeluaran_kas')
            ->where('transaksi.tanggal', '<=', $tanggal_akhir_bulan_lalu)
            ->sum('detail_transaksi.jumlah');

        $saldo_akhir_bulan_lalu = $total_pemasukan_lalu - $total_pengeluaran_lalu;

        // Pemasukan bulan ini
        $pemasukan = DB::table('transaksi')
            ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
            ->where('transaksi.jenis', 'pemasukan_kas')
            ->whereBetween('transaksi.tanggal', [$tanggal_awal, $tanggal_akhir])
            ->orderBy('transaksi.tanggal')
            ->get([
                'transaksi.tanggal',
                'transaksi.keterangan',
                'detail_transaksi.jumlah as nominal'
            ]);

        // Pengeluaran bulan ini
        $pengeluaran = DB::table('transaksi')
            ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
            ->where('transaksi.jenis', 'pengeluaran_kas')
            ->whereBetween('transaksi.tanggal', [$tanggal_awal, $tanggal_akhir])
            ->orderBy('transaksi.tanggal')
            ->get([
                'transaksi.tanggal',
                'transaksi.keterangan',
                'detail_transaksi.jumlah as nominal'
            ]);

        // Total pemasukan & pengeluaran bulan ini
        $total_pemasukan = $pemasukan->sum('nominal');
        $total_pengeluaran = $pengeluaran->sum('nominal');

        return response()->json([
            'saldo_akhir_bulan_lalu' => $saldo_akhir_bulan_lalu,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'total_pemasukan' => $total_pemasukan,
            'total_pengeluaran' => $total_pengeluaran,
        ]);
    }

    // Untuk render halaman awal
    return Inertia::render('Admin/Laporan/Laporan', [
        'title' => 'Laporan Kas',
        'flash' => ['message' => session('message')]
    ]);
}
}
