<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTabungan extends Model
{
    protected $table = 'master_tabungan';

    protected $fillable = [
        'nama',
        'jatah',
        'status',
    ];

    public function tabungan()
    {
        return $this->hasMany(TabunganMasjid::class, 'jamaah_id');
    }

    public function getTotalTabunganAttribute()
    {
        return $this->tabungan()->where('status', 1)->sum('jumlah');
    }

    public function getPercentageAttribute()
    {
        if ($this->jatah > 0) {
            return round(($this->total_tabungan / $this->jatah) * 100, 2);
        }
        return 0;
    }
}
