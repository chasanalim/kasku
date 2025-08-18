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

class ShodaqahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $shodaqah = DetailTransaksi::with(['transaksi', 'transaksi.akun', 'jamaah'])
            ->whereHas('transaksi', function ($query) {
                $query->where('jenis', 'pemasukan');
            })
            ->orderBy('id', 'desc')
            ->get();
        // return response()->json($shodaqah);
        if ($request->wantsJson()) {
            $data = DetailTransaksi::with(['transaksi', 'transaksi.akun', 'jamaah'])
                ->whereHas('transaksi', function ($query) {
                    $query->where('jenis', 'pemasukan');
                })
                ->whereNotNull('jamaah_id')
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
                        'verify_url' => route('admin.shodaqah.verify', $row->id),
                        'edit_url' => route('admin.shodaqah.edit', $row->id),
                        'delete_url' => route('admin.shodaqah.destroy', $row->id)
                    ];
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return Inertia::render('Admin/Shodaqah/Index', [
            'title' => 'Data Shodaqah Jamaah',
            'flash' => [
                'message' => session('message')
            ],
            'shodaqah' => $shodaqah,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jamaah = Jamaah::all();
        return Inertia::render('Admin/Shodaqah/Create', [
            'title' => 'Tambah Shodaqah Jamaah',
            'jamaah' => $jamaah,
            'action' => route('admin.shodaqah.store'),
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
            'persenan' => 'required|numeric',
            'jimpitan' => 'required|numeric',
            'dapur_pusat' => 'required|numeric',
            'shodaqah_daerah' => 'required|numeric',
            'shodaqah_kelompok' => 'required|numeric',
            'jumlah' => 'required|numeric'
        ]);

        DB::transaction(function () use ($request) {
            if ($request->persenan > 0) {
                $akunIR = AkunRekening::where('kode_akun', '601')->first();
                $akunIR->increment('saldo_awal', $request->persenan);
                $transaksi = Transaksi::create([
                    'tanggal' => $request->tanggal,
                    'akun_id' => $akunIR->id,
                    'keterangan' => 'Persenan dari ' . Jamaah::find($request->jamaah_id)->nama,
                    'jenis' => 'pemasukan'
                ]);
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'jamaah_id' => $request->jamaah_id,
                    'persenan' => $request->persenan,
                    'jimpitan' => 0,
                    'dapur_pusat' => 0,
                    'shodaqah_daerah' => 0,
                    'shodaqah_kelompok' => 0,
                    'jumlah' => $request->persenan,
                ]);
            }
            if ($request->jimpitan > 0) {
                $akunJatahProker = AkunRekening::where('kode_akun', '105')->first();
                $akunJatahProker->increment('saldo_awal', $request->jimpitan);
                $transaksiJatahProker = Transaksi::create([
                    'tanggal' => $request->tanggal,
                    'akun_id' => $akunJatahProker->id,
                    'keterangan' => 'Jimpitan dari ' . Jamaah::find($request->jamaah_id)->nama,
                    'jenis' => 'pemasukan'
                ]);
                DetailTransaksi::create([
                    'transaksi_id' => $transaksiJatahProker->id,
                    'jamaah_id' => $request->jamaah_id,
                    'persenan' => 0,
                    'jimpitan' => $request->jimpitan,
                    'dapur_pusat' => 0,
                    'shodaqah_daerah' => 0,
                    'shodaqah_kelompok' => 0,
                    'jumlah' => $request->jimpitan,
                ]);
            }
            if ($request->dapur_pusat > 0) {
                $akunDapurPusat = AkunRekening::where('kode_akun', '602')->first();
                $akunDapurPusat->increment('saldo_awal', $request->dapur_pusat);

                $transaksiDapurPusat = Transaksi::create([
                    'tanggal' => $request->tanggal,
                    'akun_id' => $akunDapurPusat->id,
                    'keterangan' => 'Dapur Pusat dari ' . Jamaah::find($request->jamaah_id)->nama,
                    'jenis' => 'pemasukan'
                ]);
                DetailTransaksi::create([
                    'transaksi_id' => $transaksiDapurPusat->id,
                    'jamaah_id' => $request->jamaah_id,
                    'persenan' => 0,
                    'jimpitan' => 0,
                    'dapur_pusat' => $request->dapur_pusat,
                    'shodaqah_daerah' => 0,
                    'shodaqah_kelompok' => 0,
                    'jumlah' => $request->dapur_pusat,
                ]);
            }
            if ($request->shodaqah_daerah > 0) {
                $akunShodaqahDaerah = AkunRekening::where('kode_akun', '603')->first();
                $akunShodaqahDaerah->increment('saldo_awal', $request->shodaqah_daerah);

                $transaksiShodaqahDaerah = Transaksi::create([
                    'tanggal' => $request->tanggal,
                    'akun_id' => $akunShodaqahDaerah->id,
                    'keterangan' => 'Shodaqah Daerah dari ' . Jamaah::find($request->jamaah_id)->nama,
                    'jenis' => 'pemasukan'
                ]);
                DetailTransaksi::create([
                    'transaksi_id' => $transaksiShodaqahDaerah->id,
                    'jamaah_id' => $request->jamaah_id,
                    'persenan' => 0,
                    'jimpitan' => 0,
                    'dapur_pusat' => 0,
                    'shodaqah_daerah' => $request->shodaqah_daerah,
                    'shodaqah_kelompok' => 0,
                    'jumlah' => $request->shodaqah_daerah,
                ]);
            }

            if ($request->shodaqah_kelompok > 0) {
                $akunDanaAsad = AkunRekening::where('kode_akun', '102')->first();
                $akunListrik = AkunRekening::where('kode_akun', '103')->first();
                $akunDanaSosial = AkunRekening::where('kode_akun', '104')->first();
                $sisa = $request->shodaqah_kelompok - 5000;
                if ($request->shodaqah_kelompok >= 8000) {
                    $akunDanaAsad->increment('saldo_awal', 2000);
                    $akunListrik->increment('saldo_awal', 3000);
                    $akunDanaSosial->increment('saldo_awal', $sisa);

                    $transaksiDanaAsad = Transaksi::create([
                        'tanggal' => $request->tanggal,
                        'akun_id' => $akunDanaAsad->id,
                        'keterangan' => 'Dana Asad dari ' . Jamaah::find($request->jamaah_id)->nama,
                        'jenis' => 'pemasukan'
                    ]);
                    DetailTransaksi::create([
                        'transaksi_id' => $transaksiDanaAsad->id,
                        'jamaah_id' => $request->jamaah_id,
                        'persenan' => 0,
                        'jimpitan' => 0,
                        'dapur_pusat' => 0,
                        'shodaqah_daerah' => 0,
                        'shodaqah_kelompok' => 2000,
                        'jumlah' => 2000,
                    ]);

                    $transaksiListrik = Transaksi::create([
                        'tanggal' => $request->tanggal,
                        'akun_id' => $akunListrik->id,
                        'keterangan' => 'Dana Listrik dari ' . Jamaah::find($request->jamaah_id)->nama,
                        'jenis' => 'pemasukan'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksiListrik->id,
                        'jamaah_id' => $request->jamaah_id,
                        'persenan' => 0,
                        'jimpitan' => 0,
                        'dapur_pusat' => 0,
                        'shodaqah_daerah' => 0,
                        'shodaqah_kelompok' => 3000,
                        'jumlah' => 3000,
                    ]);

                    $transaksiDanaSosial = Transaksi::create([
                        'tanggal' => $request->tanggal,
                        'akun_id' => $akunDanaSosial->id,
                        'keterangan' => 'Dana Sosial dari ' . Jamaah::find($request->jamaah_id)->nama,
                        'jenis' => 'pemasukan'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksiDanaSosial->id,
                        'jamaah_id' => $request->jamaah_id,
                        'persenan' => 0,
                        'jimpitan' => 0,
                        'dapur_pusat' => 0,
                        'shodaqah_daerah' => 0,
                        'shodaqah_kelompok' => $sisa,
                        'jumlah' => $sisa,
                    ]);
                } else {
                    $transaksiListrik = Transaksi::create([
                        'tanggal' => $request->tanggal,
                        'akun_id' => $akunListrik->id,
                        'keterangan' => 'Dana Listrik dari ' . Jamaah::find($request->jamaah_id)->nama,
                        'jenis' => 'pemasukan'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksiListrik->id,
                        'jamaah_id' => $request->jamaah_id,
                        'persenan' => 0,
                        'jimpitan' => 0,
                        'dapur_pusat' => 0,
                        'shodaqah_daerah' => 0,
                        'shodaqah_kelompok' => $request->shodaqah_kelompok,
                        'jumlah' => $request->shodaqah_kelompok,
                    ]);
                }
            }
        });

        return redirect()->route('admin.shodaqah.index')
            ->with('message', 'Data Shodaqah Jamaah berhasil ditambahkan');
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
        $jamaah = Jamaah::all();
        $shodaqah = DetailTransaksi::with(['transaksi', 'jamaah'])
            ->whereHas('transaksi', function ($query) {
                $query->where('jenis', 'pemasukan');
            })
            ->findOrFail($id);
        // return response()->json($shodaqah);
        return Inertia::render('Admin/Shodaqah/Create', [
            'title' => 'Edit Shodaqah Jamaah',
            'shodaqah' => $shodaqah,
            'jamaah' => $jamaah,
            'action' => route('admin.shodaqah.update', $shodaqah->id),
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
            'persenan' => 'required|numeric',
            'jimpitan' => 'required|numeric',
            'dapur_pusat' => 'required|numeric',
            'shodaqah_daerah' => 'required|numeric',
            'shodaqah_kelompok' => 'required|numeric',
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
                'jamaah_id' => $request->jamaah_id,
                'shodaqah' => $request->shodaqah,
                'syiar' => $request->syiar,
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

        return redirect()->route('admin.shodaqah.index')->with('message', 'Data Shodaqah Jamaah berhasil dihapus');
    }

    public function verify(string $id)
    {
        $shodaqah = DetailTransaksi::findOrFail($id);
        $shodaqah->update(['status' => 1]);

        return redirect()->route('admin.shodaqah.index')->with('message', 'Data Shodaqah Jamaah berhasil diverifikasi');
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
            return redirect()->route('admin.shodaqah.index')->with('message', 'Data Shodaqah Jamaah berhasil diverifikasi');
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
            return redirect()->route('admin.shodaqah.index')->with('message', 'Data Shodaqah Jamaah berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus data'], 500);
        }
    }
}
