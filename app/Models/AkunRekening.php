<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AkunRekening extends Model
{
    protected $table = 'akun_rekening';

    protected $fillable = [
        'nama',
        'kode_akun',
        'tipe',
        'saldo_awal',
    ];

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class , 'akun_id');
    }

}
