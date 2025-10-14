<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterJatahDesa extends Model
{
    protected $table = 'master_jatah_desa';
    protected $fillable = [
        'jatah_desa',
        'jumlah',
    ];

}
