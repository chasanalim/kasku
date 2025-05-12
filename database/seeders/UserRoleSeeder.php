<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      
        //Permission
        Permission::create(['name' => 'view-dashboard', 'guard_name' => 'web']);

        Permission::create(['name' => 'view-pemasukan', 'guard_name' => 'web']);
        Permission::create(['name' => 'add-pemasukan', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-pemasukan', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete-pemasukan', 'guard_name' => 'web']);

        Permission::create(['name' => 'view-pengeluaran', 'guard_name' => 'web']);
        Permission::create(['name' => 'add-pengeluaran', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-pengeluaran', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete-pengeluaran', 'guard_name' => 'web']);

        Permission::create(['name' => 'view-laporan', 'guard_name' => 'web']);
        Permission::create(['name' => 'add-laporan', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-laporan', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete-laporan', 'guard_name' => 'web']);

        Permission::create(['name' => 'view-qurban', 'guard_name' => 'web']);
        Permission::create(['name' => 'add-qurban', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-qurban', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete-qurban', 'guard_name' => 'web']);

        Permission::create(['name' => 'view-master', 'guard_name' => 'web']);
        Permission::create(['name' => 'add-master', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-master', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete-master', 'guard_name' => 'web']);

        Permission::create(['name' => 'view-user', 'guard_name' => 'web']);
        Permission::create(['name' => 'add-user', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-user', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete-user', 'guard_name' => 'web']);
        Permission::create(['name' => 'restore-user', 'guard_name' => 'web']);

        Permission::create(['name' => 'view-role', 'guard_name' => 'web']);
        Permission::create(['name' => 'add-role', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-role', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete-role', 'guard_name' => 'web']);

        //Role
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'bendahara', 'guard_name' => 'web']);
        Role::create(['name' => 'ketua', 'guard_name' => 'web']);


        $roleAdmin = Role::findByName('admin');
        $roleAdmin->givePermissionTo([
            'view-dashboard',
            'view-pemasukan',
            'add-pemasukan',
            'edit-pemasukan',
            'delete-pemasukan',
            'view-pengeluaran',
            'add-pengeluaran',
            'edit-pengeluaran',
            'delete-pengeluaran',
            'view-laporan',
            'add-laporan',
            'edit-laporan',
            'delete-laporan',
            'view-qurban',
            'add-qurban',
            'edit-qurban',
            'delete-qurban',
            'view-master',
            'add-master',
            'edit-master',
            'delete-master',
            'view-user',
            'add-user',
            'edit-user',
            'delete-user',
            'restore-user',
            'view-role',
            'add-role',
            'edit-role',
            'delete-role',  
        ]);

        $roleBendahara = Role::findByName('bendahara');
        $roleBendahara->givePermissionTo([
            'view-dashboard',
            'view-pemasukan',
            'add-pemasukan',
            'edit-pemasukan',
            'delete-pemasukan',
            'view-pengeluaran',
            'add-pengeluaran',
            'edit-pengeluaran',
            'delete-pengeluaran',
            'view-laporan',
            'add-laporan',
            'edit-laporan',
            'delete-laporan',
            'view-qurban',
            'add-qurban',
            'edit-qurban',
            'delete-qurban',
        ]);

        $roleKetua = Role::findByName('ketua');
        $roleKetua->givePermissionTo([
            'view-dashboard',
            'view-pemasukan',
            'view-pengeluaran',
            'view-laporan',
            'view-qurban',
        ]);

    }
}
