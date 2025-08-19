<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabunganMasjid extends Model
{
    protected $table = 'tabungan_masjid';
    protected $fillable = [
        'akun_id',
        'tanggal',
        'jamaah_id',
        'jumlah',
        'keterangan',
        'status',
    ];

    public function akun()
    {
        return $this->belongsTo(AkunRekening::class, 'akun_id');
    }

    public function jamaah()
    {
        return $this->belongsTo(MasterTabungan::class, 'jamaah_id');
    }
}
