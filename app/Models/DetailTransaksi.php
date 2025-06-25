<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    protected $table = 'detail_transaksi';

    protected $fillable = [
        'transaksi_id',
        'akun_id',
        'jamaah_id',
        'persenan',
        'dapur_pusat',
        'shodaqah_daerah',
        'shodaqah_kelompok',
        'jimpitan',
        'jumlah',
        'debit',
        'kredit',
        'status'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    public function jamaah()
    {
        return $this->belongsTo(Jamaah::class);
    }
}
