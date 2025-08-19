<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jamaah extends Model
{
    protected $table = 'jamaah';

    protected $fillable = [
        'nama',
        'kategori',
        'status',
    ];
}
