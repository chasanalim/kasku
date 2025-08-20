<?php

namespace App\Http\Controllers\Admin;

use Inertia\Inertia;
use App\Models\Jamaah;
use App\Models\Transaksi;
use App\Models\AkunRekening;
use Illuminate\Http\Request;
use App\Models\TabunganMasjid;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\MasterTabungan;

class TabunganMasjidController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tabungan = TabunganMasjid::with(['akun', 'jamaah'])
            ->orderBy('id', 'desc')
            ->get();
        // return response()->json($tabungan);
        if ($request->wantsJson()) {
            if ($request->tanggal_awal && $request->tanggal_akhir) {
                $data = TabunganMasjid::with(['akun', 'jamaah'])
                    ->orderBy('id', 'desc')
                    ->whereBetween('tanggal', [
                        $request->tanggal_awal,
                        $request->tanggal_akhir
                    ]);
            }

            $data = $data ?? TabunganMasjid::with(['akun', 'jamaah'])
                ->orderBy('id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return [
                        'verify_url' => route('admin.tabungan.verify', $row->id),
                        'edit_url' => route('admin.tabungan.edit', $row->id),
                        'delete_url' => route('admin.tabungan.destroy', $row->id)
                    ];
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return Inertia::render('Admin/TabunganMasjid/Index', [
            'title' => 'Data Tabungan Masjid Jamaah',
            'flash' => [
                'message' => session('message')
            ],
            'tabungan' => $tabungan,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jamaah = MasterTabungan::where('status', 1)
            ->orderBy('id', 'asc')
            ->get();
        return Inertia::render('Admin/TabunganMasjid/Create', [
            'title' => 'Tambah Tabungan Masjid Jamaah',
            'jamaah' => $jamaah,
            'action' => route('admin.tabungan.store'),
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
            'jamaah_id' => 'required',
            'jumlah' => 'required|numeric'
        ]);

        DB::transaction(function () use ($request) {
            if ($request->jumlah > 0) {
                $akunTabungan = AkunRekening::where('kode_akun', '604')->first();
                $akunTabungan->increment('saldo_awal', $request->jumlah);
                TabunganMasjid::create([
                    'akun_id' => $akunTabungan->id,
                    'tanggal' => $request->tanggal,
                    'jamaah_id' => $request->jamaah_id,
                    'jumlah' => $request->jumlah,
                    'keterangan' => 'Tabungan Masjid Jamaah ' . Jamaah::find($request->jamaah_id)->nama,
                    'status' => 0
                ]);
            }
        });

        return redirect()->route('admin.tabungan.index')
            ->with('message', 'Data Tabunga nMasjid Jamaah berhasil ditambahkan');
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
        $jamaah = MasterTabungan::all();
        $tabungan = TabunganMasjid::with(['akun', 'jamaah'])
            ->findOrFail($id);
        // return response()->json($tabungan);
        return Inertia::render('Admin/TabunganMasjid/Create', [
            'title' => 'Edit Tabungan Masjid Jamaah',
            'tabungan' => $tabungan,
            'jamaah' => $jamaah,
            'action' => route('admin.tabungan.update', $tabungan->id),
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
            'jamaah_id' => 'required',
            'jumlah' => 'required|numeric'
        ]);

        DB::transaction(function () use ($request, $id) {

        });

        return redirect()->route('admin.tabungan.index')
            ->with('message', 'Data TabunganMasjid Jamaah berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tabungan = TabunganMasjid::findOrFail($id);
        $tabungan->delete();

        $transaksi = Transaksi::find($tabungan->transaksi_id);
        $transaksi->delete();

        return redirect()->route('admin.tabungan.index')->with('message', 'Data TabunganMasjid Jamaah berhasil dihapus');
    }

    public function verify(string $id)
    {
        $tabungan = TabunganMasjid::findOrFail($id);
        $tabungan->update(['status' => 1]);

        return redirect()->route('admin.tabungan.index')->with('message', 'Data TabunganMasjid Jamaah berhasil diverifikasi');
    }

    public function verifyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:tabungan_masjid,id'
        ]);

        DB::beginTransaction();
        try {
            TabunganMasjid::whereIn('id', $request->ids)->update(['status' => 1]);
            DB::commit();
            return redirect()->route('admin.tabungan.index')->with('message', 'Data TabunganMasjid Jamaah berhasil diverifikasi');
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
            TabunganMasjid::whereIn('id', $request->ids)->delete();
            DB::commit();
            return redirect()->route('admin.tabungan.index')->with('message', 'Data TabunganMasjid Jamaah berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus data'], 500);
        }
    }
}
