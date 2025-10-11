<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JatahDesa extends Model
{
    protected $table = 'jatah_desa';
    protected $fillable = [
        'tanggal',
        'jenis',
        'keterangan',
        'jumlah',
    ];

    protected $dates = ['tanggal'];
}
