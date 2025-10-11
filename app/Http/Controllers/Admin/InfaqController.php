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

class InfaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $infaq = DetailTransaksi::with(['transaksi', 'transaksi.akun'])
            ->whereHas('transaksi', function ($query) {
                $query->where('jenis', 'pemasukan_kas');
                // ->where('akun_id', 1);
            })
            ->orderBy('id', 'desc')
            ->get();
        // return response()->json($infaq);

        if ($request->wantsJson()) {
            $data = DetailTransaksi::with(['transaksi', 'transaksi.akun'])
                ->whereHas('transaksi', function ($query) {
                    $query->where('jenis', 'pemasukan_kas');
                    // ->where('akun_id', 1);
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
                        'verify_url' => route('admin.infaq.verify', $row->id),
                        'edit_url' => route('admin.infaq.edit', $row->id),
                        'delete_url' => route('admin.infaq.destroy', $row->id)
                    ];
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return Inertia::render('Admin/Infaq/Index', [
            'title' => 'Data Infaq Kelompok',
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
        return Inertia::render('Admin/Infaq/Create', [
            'title' => 'Tambah InfaqJamaah',
            'action' => route('admin.infaq.store'),
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
                $akun->increment('saldo_awal', $request->jumlah);
                $transaksi = Transaksi::create([
                    'tanggal' => $request->tanggal,
                    'akun_id' => $akun->id,
                    'keterangan' => $request->keterangan,
                    'jenis' => 'pemasukan_kas'
                ]);
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'jumlah' => $request->jumlah,
                ]);
            }
        });

        return redirect()->route('admin.infaq.index')
            ->with('message', 'Data Infaq berhasil ditambahkan');
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
                $query->where('jenis', 'pemasukan_kas');
            })
            ->findOrFail($id);
        // return response()->json($infaq);
        return Inertia::render('Admin/Infaq/Create', [
            'title' => 'Edit Infaq Jamaah',
            'infaq' => $infaq,
            'action' => route('admin.infaq.update', $infaq->id),
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
                    'jenis' => 'pemasukan'
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
        DB::transaction(function () use ($id) {
            $shodaqah = DetailTransaksi::findOrFail($id);
            $transaksi = Transaksi::find($shodaqah->transaksi_id);
            $akun = AkunRekening::find($transaksi->akun_id);

            $akun->decrement('saldo_awal', $shodaqah->jumlah);

            // Delete the records
            $shodaqah->delete();
            $transaksi->delete();

            return redirect()->route('admin.infaq.index')->with('message', 'Data Infaq berhasil dihapus');
        });
    }

    public function verify(string $id)
    {
        $shodaqah = DetailTransaksi::findOrFail($id);
        $shodaqah->update(['status' => 1]);

        return redirect()->route('admin.infaq.index')->with('message', 'Data infaq Jamaah berhasil diverifikasi');
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
            return redirect()->route('admin.infaq.index')->with('message', 'Data Infaq Jamaah berhasil diverifikasi');
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
            return redirect()->route('admin.infaq.index')->with('message', 'Data Infaq Jamaah berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus data'], 500);
        }
    }
}
