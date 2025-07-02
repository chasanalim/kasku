<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Jamaah;
use App\Models\Transaksi;
use App\Models\AkunRekening;
use Illuminate\Http\Request;
use App\Models\DetailTransaksi;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class KasKelompokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $infaq = DetailTransaksi::with(['transaksi', 'transaksi.akun'])
            ->whereHas('transaksi', function ($query) {
                $query->where('jenis', 'pengeluaran_kas')
                    ->where('akun_id', 1);
            })
            ->orderBy('id', 'desc')
            ->get();
        // return response()->json($infaq);

        if ($request->wantsJson()) {
            $data = DetailTransaksi::with(['transaksi', 'transaksi.akun'])
                ->whereHas('transaksi', function ($query) {
                    $query->where('jenis', 'pengeluaran_kas')
                        ->where('akun_id', 1);
                })
                ->orderBy('id', 'desc')
                ->whereHas('transaksi', function ($query) use ($request) {
                    if ($request->tanggal_awal && $request->tanggal_akhir) {
                        $query->whereBetween('tanggal', [
                            $request->tanggal_awal,
                            $request->tanggal_akhir
                        ]);
                    }
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return [
                        'verify_url' => route('admin.kaskelompok.verify', $row->id),
                        'edit_url' => route('admin.kaskelompok.edit', $row->id),
                        'delete_url' => route('admin.kaskelompok.destroy', $row->id)
                    ];
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return Inertia::render('Admin/KasKelompok/Index', [
            'title' => 'Data Pengeluaran Kelompok',
            'flash' => [
                'message' => session('message')
            ],
            'infaq' => $infaq,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jamaah = Jamaah::all();
        return Inertia::render('Admin/KasKelompok/Create', [
            'title' => 'Tambah Pengeluaran Kelompok',
            'action' => route('admin.kaskelompok.store'),
            'method' => 'POST',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // app/Http/Controllers/TransaksiController.php
    public function store(Request $request)
    {
        // return response()->json($request->all());
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah' => 'required|numeric',
            'keterangan' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            if ($request->jumlah > 0) {
                $akun = AkunRekening::where('kode_akun', '101')->first();
                $akun->decrement('saldo_awal', $request->jumlah);
                $transaksi = Transaksi::create([
                    'tanggal' => $request->tanggal,
                    'akun_id' => $akun->id,
                    'keterangan' => $request->keterangan,
                    'jenis' => 'pengeluaran_kas'
                ]);
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'jumlah' => $request->jumlah,
                ]);
            }

        });

        return redirect()->route('admin.kaskelompok.index')
            ->with('message', 'Data Pengeluaran berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $infaq = DetailTransaksi::with(['transaksi'])
            ->whereHas('transaksi', function ($query) {
                $query->where('jenis', 'pengeluaran_kas');
            })
            ->findOrFail($id);
        // return response()->json($shodaqah);
        return Inertia::render('Admin/KasKelompok/Create', [
            'title' => 'Edit Pengeluaran Kelompok',
            'infaq' => $infaq,
            'action' => route('admin.kaskelompok.update', $infaq->id),
            'method' => 'PUT',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required',
            'jumlah' => 'required|numeric'
        ]);

        DB::transaction(function () use ($request, $id) {
            $transaksi = Transaksi::updateOrCreate(
                ['id' => $id],
                [
                    'tanggal' => $request->tanggal,
                    'keterangan' => $request->keterangan,
                    'akun_id' => 12,
                    'jenis' => 'pengeluaran_kas'
                ]
            );

            $detailData = [
                'transaksi_id' => $transaksi->id,
                'jumlah' => $request->jumlah
            ];

            DetailTransaksi::updateOrCreate(
                ['transaksi_id' => $transaksi->id, 'akun_id' => 12],
                array_merge($detailData, ['debit' => $request->jumlah, 'kredit' => 0])
            );

            $kas = AkunRekening::where('nama', 'Kas Kelompok')->first();

            DetailTransaksi::updateOrCreate(
                ['transaksi_id' => $transaksi->id, 'akun_id' => $kas->id],
                array_merge($detailData, ['debit' => 0, 'kredit' => $request->jumlah])
            );
        });

        return redirect()->route('admin.shodaqah.index')
            ->with('message', 'Data Shodaqah Jamaah berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shodaqah = DetailTransaksi::findOrFail($id);
        $shodaqah->delete();
        $transaksi = Transaksi::find($shodaqah->transaksi_id);
        $transaksi->delete();

        return redirect()->route('admin.kaskelompok.index')->with('message', 'Data Pengeluaran berhasil dihapus');
    }

    public function verify(string $id)
    {
        $shodaqah = DetailTransaksi::findOrFail($id);
        $shodaqah->update(['status' => 1]);

        return redirect()->route('admin.kaskelompok.index')->with('message', 'Data Pengeluaran berhasil diverifikasi');
    }

    public function verifyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:detail_transaksi,id'
        ]);

        DB::beginTransaction();
        try {
            DetailTransaksi::whereIn('id', $request->ids)->update(['status' => 1]);
            DB::commit();
            return redirect()->route('admin.kaskelompok.index')->with('message', 'Data Pengeluaran Kelompok berhasil diverifikasi');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memverifikasi data'], 500);
        }
    }

    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:detail_transaksi,id'
        ]);

        DB::beginTransaction();
        try {
            DetailTransaksi::whereIn('id', $request->ids)->delete();
            DB::commit();
            return redirect()->route('admin.kaskelompok.index')->with('message', 'Data Pengeluaran Kelompok berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus data'], 500);
        }
    }
}
