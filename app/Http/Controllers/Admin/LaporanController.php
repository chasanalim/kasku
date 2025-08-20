<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\DetailTransaksi;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\AkunRekening;

class LaporanController extends Controller
{
    public function rekapShodaqah(Request $request)
    {
        if ($request->wantsJson()) {
            // Parse tanggal dari request
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;

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
                AND transaksi.tanggal BETWEEN ? AND ?
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
                ->addBinding([$tanggalAwal, $tanggalAkhir], 'join')
                ->orderBy('jamaah.id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status_format', function ($row) {
                    $class = $row->status == 'Sudah Setor' ? 'success' : 'danger';
                    return "<span class='badge bg-{$class}'>{$row->status}</span>";
                })
                ->rawColumns(['status_format'])
                ->with([
                    'recordsTotal' => DB::table('jamaah')->count(),
                    'recordsSubmitted' => DB::table('jamaah')
                        ->leftJoin(DB::raw('(SELECT DISTINCT jamaah_id FROM detail_transaksi) as dt'), 'jamaah.id', '=', 'dt.jamaah_id')
                        ->whereNotNull('dt.jamaah_id')
                        ->count()
                ])
                ->make(true);
        }

        return Inertia::render('Admin/Laporan/RekapShodaqah', [
            'title' => 'Data Shodaqah Jamaah',
            'flash' => ['message' => session('message')]
        ]);
    }
    public function rekapShodaqahDesa(Request $request)
    {
        if ($request->wantsJson()) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;

            $data = DB::table('jamaah')
                ->leftJoin(DB::raw('(
                    SELECT 
                        detail_transaksi.jamaah_id,
                        MAX(transaksi.tanggal) as tanggal,
                        SUM(detail_transaksi.persenan) as persenan,
                        SUM(detail_transaksi.jimpitan) as jimpitan,
                        SUM(detail_transaksi.dapur_pusat) as dapur_pusat,
                        SUM(CASE WHEN detail_transaksi.shodaqah_daerah > 0 THEN 2000 ELSE 0 END) as kk,
                        SUM(CASE WHEN detail_transaksi.shodaqah_daerah > 0 THEN detail_transaksi.shodaqah_daerah - 2000 ELSE 0 END) as ppg,
                        0 as zakat,
                        SUM(detail_transaksi.jumlah) as jumlah,
                        COUNT(transaksi.id) as transaksi_count
                    FROM detail_transaksi
                    INNER JOIN transaksi ON detail_transaksi.transaksi_id = transaksi.id
                    WHERE transaksi.jenis = "pemasukan"
                    AND transaksi.tanggal BETWEEN ? AND ?
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
                    DB::raw('COALESCE(dt.kk, 0) as kk'),
                    DB::raw('COALESCE(dt.ppg, 0) as ppg'),
                    DB::raw('COALESCE(dt.zakat, 0) as zakat'),
                    DB::raw('COALESCE(dt.jumlah, 0) as jumlah'),
                    DB::raw('CASE WHEN dt.transaksi_count > 0 THEN "Sudah Setor" ELSE "Belum Setor" END as status')
                )
                ->addBinding([$tanggalAwal, $tanggalAkhir], 'join')
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

        return Inertia::render('Admin/Laporan/RekapShodaqahDesa', [
            'title' => 'Data Shodaqah Jamaah',
            'flash' => ['message' => session('message')]
        ]);
    }

    public function rekapTabungan(Request $request)
    {
        if ($request->wantsJson()) {
            $data = DB::table('master_tabungan')
                ->leftJoin(DB::raw('(
                    SELECT 
                        jamaah_id,
                        SUM(jumlah) as total_tabungan
                    FROM tabungan_masjid
                    WHERE status = 1
                    GROUP BY jamaah_id
                ) as tm'), 'master_tabungan.id', '=', 'tm.jamaah_id')
                ->select(
                    'master_tabungan.id as jamaah_id',
                    'master_tabungan.nama',
                    'master_tabungan.jatah',
                    DB::raw('COALESCE(tm.total_tabungan, 0) as total_tabungan'),
                    DB::raw('CASE 
                        WHEN master_tabungan.jatah > 0 
                        THEN ROUND((COALESCE(tm.total_tabungan, 0) / master_tabungan.jatah) * 100, 2)
                        ELSE 0 
                    END as percentage')
                )
                ->orderBy('master_tabungan.id', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('percentage_format', function ($row) {
                    $percentage = intval($row->percentage);
                    if ($percentage >= 100) {
                        $class = 'success';    // Hijau: >= 100%
                    } elseif ($percentage >= 75) {
                        $class = 'info';       // Biru: 75-99%
                    } elseif ($percentage >= 50) {
                        $class = 'warning';    // Kuning: 50-74%
                    } else {
                        $class = 'danger';     // Merah: < 50%
                    }
                    return "<button class='button button-sm rounded bg-{$class}'>{$percentage}%</button>";
                })
                ->rawColumns(['percentage_format'])
                ->make(true);
        }

        return Inertia::render('Admin/Laporan/RekapTabunganMasjid', [
            'title' => 'Data Tabungan Masjid',
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

    public function bukuBesar(Request $request)
    {
        if ($request->wantsJson()) {
            $bulan = $request->input('bulan', date('m'));
            $tahun = $request->input('tahun', date('Y'));

            $tanggal_awal = date("$tahun-$bulan-01");
            $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));

            // Pemasukan
            $akun = AkunRekening::where('tipe', 'kas')->get();

            $total_akun = $akun->sum('saldo_awal');

            return response()->json([
                'akun' => $akun,
                'total_akun' => $total_akun,
            ]);
        }

        // Untuk render halaman awal
        return Inertia::render('Admin/Laporan/BukuBesar', [
            'title' => 'Laporan Buku Besar',
            'flash' => ['message' => session('message')]
        ]);
    }

    public function jurnal(Request $request)
    {
        if ($request->wantsJson()) {
            $bulan = $request->input('bulan', date('m'));
            $tahun = $request->input('tahun', date('Y'));

            $tanggal_awal = date("$tahun-$bulan-01");
            $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));

            // Pemasukan
            $pemasukan = DB::table('detail_transaksi')
                ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.id')
                ->join('akun_rekening', 'transaksi.akun_id', '=', 'akun_rekening.id')
                ->where('transaksi.jenis', 'pemasukan')
                ->whereBetween('transaksi.tanggal', [$tanggal_awal, $tanggal_akhir])
                ->orderBy('transaksi.tanggal')
                ->get([
                    'transaksi.tanggal as tanggal',
                    'akun_rekening.nama as akun',
                    'detail_transaksi.jumlah as jumlah',
                    'transaksi.keterangan',
                ]);

            // Pengeluaran
            $pengeluaran = DB::table('detail_transaksi')
                ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.id')
                ->join('akun_rekening', 'transaksi.akun_id', '=', 'akun_rekening.id')
                ->where('transaksi.jenis', 'pengeluaran')
                ->whereBetween('transaksi.tanggal', [$tanggal_awal, $tanggal_akhir])
                ->orderBy('transaksi.tanggal')
                ->get([
                    'transaksi.tanggal as tanggal',
                    'akun_rekening.nama as akun',
                    'detail_transaksi.jumlah as jumlah',
                    'transaksi.keterangan',
                ]);

            $total_pemasukan = $pemasukan->sum('jumlah');
            $total_pengeluaran = $pengeluaran->sum('jumlah');

            return response()->json([
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'total_pemasukan' => $total_pemasukan,
                'total_pengeluaran' => $total_pengeluaran,
            ]);
        }

        // Untuk render halaman awal
        return Inertia::render('Admin/Laporan/PostingJurnal', [
            'title' => 'Laporan Posting Jurnal',
            'flash' => ['message' => session('message')]
        ]);
    }

    // Add this new method in LaporanController
    public function getTabunganDetail(Request $request)
    {
        $jamaahId = $request->jamaah_id;

        $details = DB::table('tabungan_masjid')
            ->where('jamaah_id', $jamaahId)
            ->where('status', 1)
            ->orderBy('tanggal', 'desc')
            ->get(['tanggal', 'jumlah']);

        return response()->json($details);
    }
}
