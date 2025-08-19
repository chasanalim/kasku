<?php

namespace Database\Seeders;

use App\Models\Jamaah;
use App\Models\JumlahTenagaKerja;
use App\Models\LamaUsaha;
use App\Models\PenerimaBanmod;
use App\Models\SkorPelatihanBanmod;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    
        $this->call([
            UserRoleSeeder::class,
            UserSeeder::class,
            AkunRekeningSeeder::class,
            JamaahSeeder::class,
            MasterTabunganSeeder::class,
        ]);
    }
}
