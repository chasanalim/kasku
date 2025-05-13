<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
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
                ->leftJoin('transaksi', function($join) use ($tanggalAwal, $tanggalAkhir) {
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
                ->addColumn('status_format', function($row) {
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
}
