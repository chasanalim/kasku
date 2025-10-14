<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Jamaah;
use App\Models\JatahDesa;
use App\Models\Transaksi;
use App\Models\AkunRekening;
use Illuminate\Http\Request;
use App\Models\DetailTransaksi;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\MasterJatahDesa;

class JatahDesaController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            $data = MasterJatahDesa::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return [
                        'edit_url' => route('admin.jatah_desa.edit', $row->id),
                        'delete_url' => route('admin.jatah_desa.destroy', $row->id)
                    ];
                })
                ->make(true);
        }

        return Inertia::render('Admin/MasterJatahDesa/Index', [
            'title' => 'Master Jatah Desa',
            'flash' => [
                'message' => session('message')
            ],
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/MasterJatahDesa/Create', [
            'title' => 'Tambah Master Jatah Desa',
            'action' => route('admin.jatah_desa.store'),
            'method' => 'POST',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jatah_desa' => 'required|string|max:255',
            'jumlah' => 'required',
        ]);

        MasterJatahDesa::create([
            'jatah_desa' => $request->jatah_desa,
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('admin.jatah_desa.index')
            ->with('message', 'Master Jatah Data Desa berhasil ditambahkan');
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
        $jatah = MasterJatahDesa::findOrFail($id);

        return Inertia::render('Admin/MasterJatahDesa/Create', [
            'title' => 'Edit Master Jatah Desa',
            'jatah' => $jatah,
            'action' => route('admin.jatah_desa.update', $jatah->id),
            'method' => 'PUT',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jatah = MasterJatahDesa::findOrFail($id);

        $request->validate([
            'jatah_desa' => 'required',
            'jumlah' => 'required',
        ]);

        $jatah->update([
            'jatah_desa' => $request->jatah_desa,
            'jumlah' => $request->jumlah,
        ]);

        return redirect()->route('admin.jatah_desa.index')
            ->with('message', 'Master Jatah Desa berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jatah = MasterJatahDesa::findOrFail($id);
        $jatah->delete();

        return redirect()->route('admin.jatah_desa.index')->with('message', 'Master Jatah Desa berhasil dihapus');
    }



    public function sync(Request $request)
    {


        try {
            // Validate request
            $request->validate([
                'bulan' => 'required|numeric|min:1|max:12',
                'tahun' => 'required|numeric|min:2000|max:2100',
                'jatahProker' => 'required|numeric|min:0',
                'infaqJumat' => 'required|numeric|min:0'
            ]);

            // Check if data already exists for the selected month
            $existingData = JatahDesa::whereMonth('tanggal', $request->bulan)
                ->whereYear('tanggal', $request->tahun)
                ->exists();

            $namaBulan = Carbon::create()->month($request->bulan)->translatedFormat('F');

            if ($existingData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data sudah disinkronisasi untuk bulan ' . $namaBulan . ' ini '
                ], 422);
            }

            DB::beginTransaction();

            // Get all required accounts
            $accounts = [
                'kas_kelompok' => AkunRekening::where('kode_akun', '101')->first(),
                'ir' => AkunRekening::where('kode_akun', '601')->first(),
                'jatah_proker' => AkunRekening::where('kode_akun', '105')->first(),
                'dapur_pusat' => AkunRekening::where('kode_akun', '106')->first(),
                'shodaqah_daerah' => AkunRekening::where('kode_akun', '602')->first(),
                'jimpitan' => AkunRekening::where('kode_akun', '605')->first(),
                'masjid' => AkunRekening::where('kode_akun', '604')->first(),
            ];

            // Clear existing JatahDesa data for the selected month
            JatahDesa::whereMonth('tanggal', $request->bulan)
                ->whereYear('tanggal', $request->tahun)
                ->delete();

            // Sync IR (Persenan)
            if ($accounts['ir']) {
                JatahDesa::create([
                    'tanggal' => date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'jenis' => 'berubah',
                    'keterangan' => 'IR',
                    'jumlah' => $accounts['ir']->saldo_awal,
                ]);

                $akun = $accounts['ir'];

                if ($accounts['ir']->saldo_awal > 0) {
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Setor ke Desa',
                        'jenis' => 'pengeluaran'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $accounts['ir']->saldo_awal,
                    ]);

                    $akun->decrement('saldo_awal', $accounts['ir']->saldo_awal);
                }
            }

            // Sync Dapur Pusat
            if ($accounts['dapur_pusat']) {
                $jatahDapurPusat = MasterJatahDesa::where('jatah_desa', 'DAPUR PUSAT / MINI')->first()->jumlah ?? 0;
                JatahDesa::create([
                    'tanggal' => date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'jenis' => 'tetap',
                    'keterangan' => 'DAPUR PUSAT / MINI',
                    'jumlah' => $jatahDapurPusat,
                ]);

                // if ($sisaJatahDapurPusat < 0) {
                // }

                $akun = $accounts['dapur_pusat'];
                $transaksi = Transaksi::create([
                    'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'akun_id' => $akun->id,
                    'keterangan' => 'Setor ke Desa',
                    'jenis' => 'pengeluaran'
                ]);

                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'jumlah' => $jatahDapurPusat,
                ]);

                $akun->decrement('saldo_awal', $jatahDapurPusat);
            }

            // Sync Shodaqah Daerah (minus 2000)
            if ($accounts['shodaqah_daerah']) {
                $jatahPPG = MasterJatahDesa::where('jatah_desa', 'PPG')->first()->jumlah ?? 0;;
                $jatahMTDaerah = MasterJatahDesa::where('jatah_desa', 'MT DAERAH')->first()->jumlah ?? 0;;
                $jatahKeamananPondok = MasterJatahDesa::where('jatah_desa', 'KEAMANAN PONDOK')->first()->jumlah ?? 0;;
                $jatahKK = MasterJatahDesa::where('jatah_desa', 'KK')->first()->jumlah ?? 0;;

                JatahDesa::create([
                    'tanggal' => date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'jenis' => 'tetap',
                    'keterangan' => 'PPG',
                    'jumlah' => $jatahPPG,
                ]);

                JatahDesa::create([
                    'tanggal' => date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'jenis' => 'tetap',
                    'keterangan' => 'MT DAERAH',
                    'jumlah' => $jatahMTDaerah,
                ]);

                JatahDesa::create([
                    'tanggal' => date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'jenis' => 'tetap',
                    'keterangan' => 'KEAMANAN PONDOK',
                    'jumlah' => $jatahKeamananPondok,
                ]);

                JatahDesa::create([
                    'tanggal' => date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'jenis' => 'tetap',
                    'keterangan' => 'KK',
                    'jumlah' => $jatahKK,
                ]);

                $sisaShodaqahDaerah = $accounts['shodaqah_daerah']->saldo_awal - ($jatahPPG + $jatahMTDaerah + $jatahKeamananPondok + $jatahKK);

                if ($sisaShodaqahDaerah < 0) {
                    $akun = $accounts['dapur_pusat'];
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Mencukupi kekurangan Shodaqah Daerah',
                        'jenis' => 'pengeluaran'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $sisaShodaqahDaerah * -1,
                    ]);

                    $akun->decrement('saldo_awal', $sisaShodaqahDaerah);

                    $akun = $accounts['shodaqah_daerah'];
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Setor ke Desa',
                        'jenis' => 'pengeluaran'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $accounts['shodaqah_daerah']->saldo_awal,
                    ]);

                    $akun->decrement('saldo_awal', $accounts['shodaqah_daerah']->saldo_awal);
                } else {
                    //tambah kas kelompok
                    $akun = $accounts['kas_kelompok'];
                    $akun->increment('saldo_awal', $sisaShodaqahDaerah);
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Sisa Shodaqah Daerah dari Amplop IR',
                        'jenis' => 'pemasukan_kas'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $sisaShodaqahDaerah,
                    ]);

                    //mengenolkan kas penampung
                    $akun = $accounts['shodaqah_daerah'];
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Setor PPG ke Desa',
                        'jenis' => 'pengeluaran'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $jatahPPG,
                    ]);

                    $akun->decrement('saldo_awal', $jatahPPG);

                    $akun = $accounts['shodaqah_daerah'];
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Setor MR Daerah ke Desa',
                        'jenis' => 'pengeluaran'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $jatahMTDaerah,
                    ]);

                    $akun->decrement('saldo_awal', $jatahMTDaerah);

                    $akun = $accounts['shodaqah_daerah'];
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Setor Keamanan Pondok ke Desa',
                        'jenis' => 'pengeluaran'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $jatahKeamananPondok,
                    ]);

                    $akun->decrement('saldo_awal', $jatahKeamananPondok);

                    $akun = $accounts['shodaqah_daerah'];
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Setor KK ke Desa',
                        'jenis' => 'pengeluaran'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $jatahKK,
                    ]);

                    $akun->decrement('saldo_awal', $jatahKK);

                    $akun = $accounts['shodaqah_daerah'];
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Masuk ke Kas Kelompok',
                        'jenis' => 'pengeluaran'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $sisaShodaqahDaerah,
                    ]);

                    $akun->decrement('saldo_awal', $sisaShodaqahDaerah);
                }
            }

            if ($accounts['jimpitan']) {
                $jatahJimpitan = MasterJatahDesa::where('jatah_desa', 'JIMPITAN')->first()->jumlah ?? 0;;

                JatahDesa::create([
                    'tanggal' => date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'jenis' => 'tetap',
                    'keterangan' => 'JIMPITAN',
                    'jumlah' => $jatahJimpitan,
                ]);

                $sisaJimpitan = $accounts['jimpitan']->saldo_awal - $jatahJimpitan;

                if ($sisaJimpitan < 0) {
                    $akun = $accounts['dapur_pusat'];
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Mencukupi kekurangan Jimpitan',
                        'jenis' => 'pengeluaran'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $sisaJimpitan * -1,
                    ]);

                    $akun->decrement('saldo_awal', $sisaJimpitan);

                    $akun = $accounts['jimpitan'];
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Setor Jimpitan ke Desa',
                        'jenis' => 'pengeluaran'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $accounts['jimpitan']->saldo_awal,
                    ]);

                    $akun->decrement('saldo_awal', $accounts['jimpitan']->saldo_awal);
                } else {
                    $akun = $accounts['kas_kelompok'];
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Sisa Jimpitan dari Amplop IR',
                        'jenis' => 'pemasukan_kas'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $sisaJimpitan,
                    ]);

                    $akun->increment('saldo_awal', $sisaJimpitan);

                    $akun = $accounts['jimpitan'];
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Setor Jimpitan ke Desa',
                        'jenis' => 'pengeluaran'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $jatahJimpitan,
                    ]);

                    $akun->decrement('saldo_awal', $jatahJimpitan);

                    $akun = $accounts['jimpitan'];
                    $transaksi = Transaksi::create([
                        'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                        'akun_id' => $akun->id,
                        'keterangan' => 'Masuk ke Kas Kelompok',
                        'jenis' => 'pengeluaran'
                    ]);

                    DetailTransaksi::create([
                        'transaksi_id' => $transaksi->id,
                        'jumlah' => $sisaJimpitan,
                    ]);

                    $akun->decrement('saldo_awal', $sisaJimpitan);
                }
            }

            if ($accounts['masjid']) {
                JatahDesa::create([
                    'tanggal' => date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'jenis' => 'berubah',
                    'keterangan' => 'SHODAQOH DAERAH (SENILAI KURBAN)',
                    'jumlah' => $accounts['masjid']->saldo_awal,
                ]);

                $akun = $accounts['masjid'];
                $transaksi = Transaksi::create([
                    'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'akun_id' => $akun->id,
                    'keterangan' => 'Setor ke Desa',
                    'jenis' => 'pengeluaran'
                ]);
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'jumlah' => $accounts['masjid']->saldo_awal,
                ]);

                $akun->decrement('saldo_awal', $accounts['masjid']->saldo_awal);
            }

            if ($accounts['jatah_proker']) {
                JatahDesa::create([
                    'tanggal' => date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'jenis' => 'berubah',
                    'keterangan' => 'JATAH PROKER',
                    'jumlah' => $request->jatahProker,
                ]);

                $akun = $accounts['jatah_proker'];
                $transaksi = Transaksi::create([
                    'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'akun_id' => $akun->id,
                    'keterangan' => 'Setor ke Desa',
                    'jenis' => 'pengeluaran'
                ]);
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'jumlah' => $request->jatahProker,
                ]);

                $akun->decrement('saldo_awal', $request->jatahProker);
            }

            if ($accounts['kas_kelompok']) {
                JatahDesa::create([
                    'tanggal' => date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'jenis' => 'berubah',
                    'keterangan' => 'INFAQ 2/3 JUMATAN',
                    'jumlah' => $request->infaqJumat,
                ]);

                $akun = $accounts['kas_kelompok'];
                $transaksi = Transaksi::create([
                    'tanggal' =>  date("Y-m-d", strtotime("{$request->tahun}-{$request->bulan}-01")),
                    'akun_id' => $akun->id,
                    'keterangan' => 'Setor Infaq 2/3 Jumat ke Desa',
                    'jenis' => 'pengeluaran_kas'
                ]);
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'jumlah' => $request->infaqJumat,
                ]);

                $akun->decrement('saldo_awal', $request->infaqJumat);
            }

            //

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disinkronkan'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan sinkronisasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
