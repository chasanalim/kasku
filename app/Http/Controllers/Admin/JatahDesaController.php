<?php

namespace App\Http\Controllers\Admin;

use App\Models\Jamaah;
use App\Models\Transaksi;
use App\Models\AkunRekening;
use Illuminate\Http\Request;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\JatahDesa;

class JatahDesaController extends Controller
{
    public function sync(Request $request)
    {
        try {
            // Check if data already exists for the selected month
            $existingData = JatahDesa::whereMonth('tanggal', $request->bulan)
                ->whereYear('tanggal', $request->tahun)
                ->exists();

            if ($existingData) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data sudah disinkronisasi untuk bulan ' . $request->bulan . ' ini '
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

            // Sync Dapur Pusat
            if ($accounts['dapur_pusat']) {
                $jatahDapurPusat = 900000;
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
                $jatahPPG = 200000;
                $jatahMTDaerah = 200000;
                $jatahKeamananPondok = 50000;
                $jatahKK = 50000;

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
                $jatahJimpitan = 100000;

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
