<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $admin = User::create([
            'name' => 'Super Admin',
            'nik' => '3571010000000001',
            'email' => 'admin@kasku.com',
            'email_verified_at' => now(),
            'phone_number' => '085854445232',
            'password' => Hash::make('kasku123'),
        ]);
        $admin->assignRole('admin');

        $bendahara = User::create([
            'name' => 'Bendahara ',
            'nik' => '3571010000000002',
            'email' => 'bendahara@kasku.com',
            'email_verified_at' => now(),
            'phone_number' => '085133564587',
            'password' => Hash::make('kasku123'),
        ]);
        $bendahara->assignRole('bendahara');

        $ketua = User::create([
            'name' => 'Ketua ',
            'nik' => '3571010000000003',
            'email' => 'ketua@kasku.com',
            'email_verified_at' => now(),
            'phone_number' => '085133564582',
            'password' => Hash::make('kasku123'),
        ]);
        $ketua->assignRole('ketua');

    }
}
