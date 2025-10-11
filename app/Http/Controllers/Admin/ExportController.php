<?php

namespace App\Http\Controllers\Admin;

use Barryvdh\DomPDF\PDF;
use App\Models\AkunRekening;
use Illuminate\Http\Request;
use App\Exports\ShodaqahExport;
use Illuminate\Support\Facades\DB;
use App\Exports\ShodaqahDesaExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportPDF(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        // Subquery untuk filter transaksi dulu
        $filteredTransaksi = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.id')
            ->where('transaksi.jenis', 'pemasukan')
            ->when($tanggalAwal && $tanggalAkhir, function ($query) use ($tanggalAwal, $tanggalAkhir) {
                $query->whereBetween('transaksi.tanggal', [$tanggalAwal, $tanggalAkhir])
                    ->whereNotNull('transaksi.tanggal');
            })
            ->select(
                'detail_transaksi.jamaah_id',
                DB::raw('SUM(detail_transaksi.persenan) as persenan'),
                DB::raw('SUM(detail_transaksi.jimpitan) as jimpitan'),
                DB::raw('SUM(detail_transaksi.dapur_pusat) as dapur_pusat'),
                DB::raw('SUM(detail_transaksi.shodaqah_daerah) as shodaqah_daerah'),
                DB::raw('SUM(detail_transaksi.shodaqah_kelompok) as shodaqah_kelompok'),
                DB::raw('SUM(detail_transaksi.jumlah) as jumlah')
            )
            ->groupBy('detail_transaksi.jamaah_id');

        // Join jamaah dengan subquery
        $data = DB::table('jamaah')
            ->leftJoinSub($filteredTransaksi, 't', function ($join) {
                $join->on('jamaah.id', '=', 't.jamaah_id');
            })
            ->select(
                'jamaah.id as no',
                'jamaah.nama',
                DB::raw('COALESCE(t.persenan, 0) as persenan'),
                DB::raw('COALESCE(t.jimpitan, 0) as jimpitan'),
                DB::raw('COALESCE(t.dapur_pusat, 0) as dapur_pusat'),
                DB::raw('COALESCE(t.shodaqah_daerah, 0) as shodaqah_daerah'),
                DB::raw('COALESCE(t.shodaqah_kelompok, 0) as shodaqah_kelompok'),
                DB::raw('COALESCE(t.jumlah, 0) as jumlah')
            )
            ->orderBy('jamaah.id')
            ->get();
        // return response()->json($data);
        $pdf = app(PDF::class);
        $pdf->setPaper('a4', 'landscape');
        $pdf->loadView('exports.shodaqah-pdf', [
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir
        ]);

        return $pdf->stream('rekap-shodaqah.pdf');
    }

    public function exportExcel(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        // Subquery untuk filter transaksi dulu
        $filteredTransaksi = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.id')
            ->where('transaksi.jenis', 'pemasukan')
            ->when($tanggalAwal && $tanggalAkhir, function ($query) use ($tanggalAwal, $tanggalAkhir) {
                $query->whereBetween('transaksi.tanggal', [$tanggalAwal, $tanggalAkhir])
                    ->whereNotNull('transaksi.tanggal');
            })
            ->select(
                'detail_transaksi.jamaah_id',
                DB::raw('SUM(detail_transaksi.persenan) as persenan'),
                DB::raw('SUM(detail_transaksi.jimpitan) as jimpitan'),
                DB::raw('SUM(detail_transaksi.dapur_pusat) as dapur_pusat'),
                DB::raw('SUM(detail_transaksi.shodaqah_daerah) as shodaqah_daerah'),
                DB::raw('SUM(detail_transaksi.shodaqah_kelompok) as shodaqah_kelompok'),
                DB::raw('SUM(detail_transaksi.jumlah) as jumlah')
            )
            ->groupBy('detail_transaksi.jamaah_id');

        // Join jamaah dengan subquery
        $data = DB::table('jamaah')
            ->leftJoinSub($filteredTransaksi, 't', function ($join) {
                $join->on('jamaah.id', '=', 't.jamaah_id');
            })
            ->select(
                'jamaah.id as no',
                'jamaah.nama',
                DB::raw('COALESCE(t.persenan, 0) as persenan'),
                DB::raw('COALESCE(t.jimpitan, 0) as jimpitan'),
                DB::raw('COALESCE(t.dapur_pusat, 0) as dapur_pusat'),
                DB::raw('COALESCE(t.shodaqah_daerah, 0) as shodaqah_daerah'),
                DB::raw('COALESCE(t.shodaqah_kelompok, 0) as shodaqah_kelompok'),
                DB::raw('COALESCE(t.jumlah, 0) as jumlah')
            )
            ->orderBy('jamaah.id')
            ->get();

        // return response()->json($data);

        return Excel::download(new ShodaqahExport($data, $tanggalAwal, $tanggalAkhir), 'rekap-shodaqah.xlsx');
    }

    public function exportPDFDesa(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        // Subquery: filter transaksi and hitung total per jamaah
        $filteredTransaksi = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.id')
            ->where('transaksi.jenis', 'pemasukan')
            ->when($tanggalAwal && $tanggalAkhir, function ($query) use ($tanggalAwal, $tanggalAkhir) {
                $query->whereBetween('transaksi.tanggal', [$tanggalAwal, $tanggalAkhir])
                    ->whereNotNull('transaksi.tanggal');
            })
            ->select(
                'detail_transaksi.jamaah_id',
                DB::raw('SUM(detail_transaksi.persenan) as persenan'),
                DB::raw('SUM(detail_transaksi.jimpitan) as jimpitan'),
                DB::raw('SUM(detail_transaksi.dapur_pusat) as dapur_pusat'),
                DB::raw('SUM(CASE WHEN detail_transaksi.shodaqah_daerah > 0 THEN 2000 ELSE 0 END) as kk'),
                DB::raw('SUM(CASE WHEN detail_transaksi.shodaqah_daerah > 0 THEN detail_transaksi.shodaqah_daerah - 2000 ELSE 0 END) as ppg'),
                DB::raw('0 as zakat'),
                DB::raw('SUM(detail_transaksi.jumlah - detail_transaksi.shodaqah_kelompok) as jumlah')
            )
            ->groupBy('detail_transaksi.jamaah_id');

        // Join jamaah dengan subquery
        $data = DB::table('jamaah')
            ->leftJoinSub($filteredTransaksi, 't', function ($join) {
                $join->on('jamaah.id', '=', 't.jamaah_id');
            })
            ->select(
                'jamaah.id as no',
                'jamaah.nama',
                DB::raw('COALESCE(t.persenan, 0) as persenan'),
                DB::raw('COALESCE(t.jimpitan, 0) as jimpitan'),
                DB::raw('COALESCE(t.dapur_pusat, 0) as dapur_pusat'),
                DB::raw('COALESCE(t.kk, 0) as kk'),
                DB::raw('COALESCE(t.ppg, 0) as ppg'),
                DB::raw('COALESCE(t.zakat, 0) as zakat'),
                DB::raw('COALESCE(t.jumlah, 0) as jumlah')
            )
            ->orderBy('jamaah.id')
            ->get();
        // return response()->json($data);
        $pdf = app(PDF::class);
        $pdf->setPaper('a4', 'landscape');
        $pdf->loadView('exports.shodaqahdesa-pdf', [
            'data' => $data,
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir
        ]);

        return $pdf->stream('rekap-shodaqahdesa.pdf');
    }

    public function exportExcelDesa(Request $request)
    {
        $tanggalAwal = $request->tanggal_awal;
        $tanggalAkhir = $request->tanggal_akhir;

        // Subquery: filter transaksi dan hitung total per jamaah
        $filteredTransaksi = DB::table('detail_transaksi')
            ->join('transaksi', 'detail_transaksi.transaksi_id', '=', 'transaksi.id')
            ->where('transaksi.jenis', 'pemasukan')
            ->when($tanggalAwal && $tanggalAkhir, function ($query) use ($tanggalAwal, $tanggalAkhir) {
                $query->whereBetween('transaksi.tanggal', [$tanggalAwal, $tanggalAkhir])
                    ->whereNotNull('transaksi.tanggal');
            })
            ->select(
                'detail_transaksi.jamaah_id',
                DB::raw('SUM(detail_transaksi.persenan) as persenan'),
                DB::raw('SUM(detail_transaksi.jimpitan) as jimpitan'),
                DB::raw('SUM(detail_transaksi.dapur_pusat) as dapur_pusat'),
                DB::raw('SUM(CASE WHEN detail_transaksi.shodaqah_daerah > 0 THEN 2000 ELSE 0 END) as kk'),
                DB::raw('SUM(CASE WHEN detail_transaksi.shodaqah_daerah > 0 THEN detail_transaksi.shodaqah_daerah - 2000 ELSE 0 END) as ppg'),
                DB::raw('0 as zakat'),
                DB::raw('SUM(detail_transaksi.jumlah - detail_transaksi.shodaqah_kelompok) as jumlah')
            )
            ->groupBy('detail_transaksi.jamaah_id');

        // Join jamaah dengan subquery
        $data = DB::table('jamaah')
            ->leftJoinSub($filteredTransaksi, 't', function ($join) {
                $join->on('jamaah.id', '=', 't.jamaah_id');
            })
            ->select(
                'jamaah.id as no',
                'jamaah.nama',
                DB::raw('COALESCE(t.persenan, 0) as persenan'),
                DB::raw('COALESCE(t.jimpitan, 0) as jimpitan'),
                DB::raw('COALESCE(t.dapur_pusat, 0) as dapur_pusat'),
                DB::raw('COALESCE(t.kk, 0) as kk'),
                DB::raw('COALESCE(t.ppg, 0) as ppg'),
                DB::raw('COALESCE(t.zakat, 0) as zakat'),
                DB::raw('COALESCE(t.jumlah, 0) as jumlah')
            )
            ->orderBy('jamaah.id')
            ->get();

        // return response()->json($data);

        return Excel::download(new ShodaqahDesaExport($data, $tanggalAwal, $tanggalAkhir), 'rekap-shodaqah.xlsx');
    }

    public function exportPDFLaporan(Request $request)
    {
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

        // Ambil data Kas Kelompok
        $kas_kelompok = DB::table('transaksi')
            ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
            ->where('transaksi.jenis', 'pemasukan')
            ->where('transaksi.keterangan', 'Kas Kelompok')
            ->whereBetween('transaksi.tanggal', [$tanggal_awal, $tanggal_akhir])
            ->select(
                DB::raw('MAX(transaksi.tanggal) as tanggal'),
                DB::raw('SUM(detail_transaksi.jumlah) as total_nominal')
            )
            ->first();

        // Tambahkan data Kas Kelompok ke pemasukan jika ada
        if ($kas_kelompok && $kas_kelompok->total_nominal > 0) {
            $pemasukan = $pemasukan->push((object)[
                'tanggal' => $kas_kelompok->tanggal,
                'keterangan' => 'Kas Kelompok dari Amplop IR',
                'nominal' => $kas_kelompok->total_nominal
            ]);
        }

        $pemasukan = $pemasukan->sortBy('tanggal')->values();

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
        $saldo_akhir = $saldo_akhir_bulan_lalu + $total_pemasukan - $total_pengeluaran;

        $pdf = app(\Barryvdh\DomPDF\PDF::class);
        $pdf->setPaper('a4', 'landscape');
        $pdf->loadView('exports.laporan-pdf', [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'saldo_akhir_bulan_lalu' => $saldo_akhir_bulan_lalu,
            'pemasukan' => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'total_pemasukan' => $total_pemasukan + $saldo_akhir_bulan_lalu,
            'total_pengeluaran' => $total_pengeluaran,
            'saldo_akhir' => $saldo_akhir,
        ]);

        return $pdf->stream('laporan.pdf');
    }
    public function exportPDFLaporanBuku(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // Format tanggal awal & akhir bulan ini
        $tanggal_awal = date("$tahun-$bulan-01");
        $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));

        // Format tanggal bulan lalu
        $bulan_lalu = date('m', strtotime('-1 month', strtotime($tanggal_awal)));
        $tahun_lalu = $bulan == 1 ? $tahun - 1 : $tahun;
        $tanggal_akhir_bulan_lalu = date("Y-m-t", strtotime("$tahun_lalu-$bulan_lalu-01"));

        // Ambil semua akun kas
        $akun = AkunRekening::where('tipe', 'kas')->get();

        // Prepare array untuk menyimpan data akun yang sudah diolah
        $processed_akun = [];
        $total = [
            'saldo_bulan_lalu' => 0,
            'pemasukan' => 0,
            'pengeluaran' => 0,
            'saldo_sekarang' => 0
        ];

        foreach ($akun as $kas) {
            // Hitung total pemasukan & pengeluaran bulan lalu
            $total_pemasukan_lalu = DB::table('transaksi')
                ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
                ->where(function ($query) {
                    $query->where('transaksi.jenis', 'pemasukan')
                        ->orWhere('transaksi.jenis', 'pemasukan_kas');
                })
                ->where('transaksi.akun_id', $kas->id)
                ->where('transaksi.tanggal', '<=', $tanggal_akhir_bulan_lalu)
                ->sum('detail_transaksi.jumlah');

            $total_pengeluaran_lalu = DB::table('transaksi')
                ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
                ->where(function ($query) {
                    $query->where('transaksi.jenis', 'pengeluaran')
                        ->orWhere('transaksi.jenis', 'pengeluaran_kas');
                })
                ->where('transaksi.akun_id', $kas->id)
                ->where('transaksi.tanggal', '<=', $tanggal_akhir_bulan_lalu)
                ->sum('detail_transaksi.jumlah');

            // Hitung pemasukan & pengeluaran bulan ini
            $pemasukan = DB::table('transaksi')
                ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
                ->where(function ($query) {
                    $query->where('transaksi.jenis', 'pemasukan')
                        ->orWhere('transaksi.jenis', 'pemasukan_kas');
                })
                ->where('transaksi.akun_id', $kas->id)
                ->whereBetween('transaksi.tanggal', [$tanggal_awal, $tanggal_akhir])
                ->sum('detail_transaksi.jumlah');

            $pengeluaran = DB::table('transaksi')
                ->join('detail_transaksi', 'transaksi.id', '=', 'detail_transaksi.transaksi_id')
                ->where(function ($query) {
                    $query->where('transaksi.jenis', 'pengeluaran')
                        ->orWhere('transaksi.jenis', 'pengeluaran_kas');
                })
                ->where('transaksi.akun_id', $kas->id)
                ->whereBetween('transaksi.tanggal', [$tanggal_awal, $tanggal_akhir])
                ->sum('detail_transaksi.jumlah');

            // Hitung saldo
            $saldo_bulan_lalu = $total_pemasukan_lalu - $total_pengeluaran_lalu;
            $saldo_sekarang = $saldo_bulan_lalu + $pemasukan - $pengeluaran;

            // Tambahkan data ke array processed_akun
            $processed_akun[] = (object)[
                'nama' => $kas->nama,
                'saldo_bulan_lalu' => $saldo_bulan_lalu,
                'pemasukan' => $pemasukan,
                'pengeluaran' => $pengeluaran,
                'saldo_sekarang' => $saldo_sekarang
            ];

            // Update total
            $total['saldo_bulan_lalu'] += $saldo_bulan_lalu;
            $total['pemasukan'] += $pemasukan;
            $total['pengeluaran'] += $pengeluaran;
            $total['saldo_sekarang'] += $saldo_sekarang;
        }

        $pdf = app(\Barryvdh\DomPDF\PDF::class);
        $pdf->setPaper('a4', 'landscape');
        $pdf->loadView('exports.laporanbuku-pdf', [
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'akun' => $processed_akun, // Kirim data yang sudah diolah
            'total' => $total
        ]);

        return $pdf->stream('laporanbuku.pdf');
    }

    public function exportPDFLaporanDesa(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // Format tanggal awal & akhir bulan ini
        $tanggal_awal = date("$tahun-$bulan-01");
        $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));

        $jatah = DB::table('jatah_desa')
            ->select(
                'id',
                'tanggal',
                'keterangan',
                'jumlah',
            )
            ->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->orderBy('tanggal')
            ->get();

        // Calculate total
        $total_jatah = $jatah->sum('jumlah');

        $pdf = app(\Barryvdh\DomPDF\PDF::class);
        $pdf->setPaper('b5', 'portrait'); // Changed to portrait orientation
        $pdf->loadView('exports.laporandesa-pdf', [
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'jatah' => $jatah,
            'total_jatah' => $total_jatah
        ]);

        return $pdf->stream('laporandesa.pdf');
    }
}
