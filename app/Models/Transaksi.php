<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = [
        'tanggal',
        'akun_id',
        'keterangan',
        'jenis',
    ];
    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class);
    }

    public function akun()
    {
        return $this->belongsTo(AkunRekening::class);
    }

}
