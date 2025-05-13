<?php

namespace App\Http\Controllers\Admin;

use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShodaqahExport;

class ExportController extends Controller
{
    public function exportPDF(Request $request)
    {
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
                'jamaah.nama',
                DB::raw('COALESCE(SUM(detail_transaksi.persenan), 0) as persenan'),
                DB::raw('COALESCE(SUM(detail_transaksi.jimpitan), 0) as jimpitan'),
                DB::raw('COALESCE(SUM(detail_transaksi.dapur_pusat), 0) as dapur_pusat'),
                DB::raw('COALESCE(SUM(detail_transaksi.shodaqah_daerah), 0) as shodaqah_daerah'),
                DB::raw('COALESCE(SUM(detail_transaksi.shodaqah_kelompok), 0) as shodaqah_kelompok'),
                DB::raw('COALESCE(SUM(detail_transaksi.jumlah), 0) as jumlah')
            )
            ->groupBy('jamaah.id', 'jamaah.nama')
            ->orderBy('jamaah.id')
            ->get();
        // return response()->json($data);
        $pdf = app(PDF::class);
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
                'jamaah.id as no',
                'jamaah.nama',
                DB::raw('COALESCE(SUM(detail_transaksi.persenan), 0) as persenan'),
                DB::raw('COALESCE(SUM(detail_transaksi.jimpitan), 0) as jimpitan'),
                DB::raw('COALESCE(SUM(detail_transaksi.dapur_pusat), 0) as dapur_pusat'),
                DB::raw('COALESCE(SUM(detail_transaksi.shodaqah_daerah), 0) as shodaqah_daerah'),
                DB::raw('COALESCE(SUM(detail_transaksi.shodaqah_kelompok), 0) as shodaqah_kelompok'),
                DB::raw('COALESCE(SUM(detail_transaksi.jumlah), 0) as jumlah')
            )
            ->groupBy('jamaah.id', 'jamaah.nama')
            ->orderBy('jamaah.id')
            ->get();

        // return response()->json($data);

        return Excel::download(new ShodaqahExport($data, $tanggalAwal, $tanggalAkhir), 'rekap-shodaqah.xlsx');
    }
}
