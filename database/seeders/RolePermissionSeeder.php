<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan cache permission
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar permission
        $permissions = [
            'barang.view',
            'barang.create',
            'barang.update',
            'barang.delete',

            'stok.view',
            'stok.update',

            'penerimaan.view',
            'penerimaan.create',
            'penerimaan.update',
            'penerimaan.delete',

            'pengeluaran.view',
            'pengeluaran.create',
            'pengeluaran.update',
            'pengeluaran.delete',

            'supplier.view',
            'supplier.create',
            'supplier.update',
            'supplier.delete',

            'laporan.view',

            'user.view',
            'user.create',
            'user.update',
            'user.delete',

            'cabang.view',
            'cabang.manage',
        ];

        // Simpan permission ke database
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // 1. SUPER ADMIN: seluruh permission
        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        // 2. ADMIN PUSAT: pengelolaan operasional dan user
        $adminPusat = Role::firstOrCreate([
            'name' => 'Admin Pusat',
            'guard_name' => 'web',
        ]);

        $adminPusat->syncPermissions([
            'barang.view',
            'barang.create',
            'barang.update',
            'barang.delete',

            'stok.view',
            'stok.update',

            'penerimaan.view',
            'penerimaan.create',
            'penerimaan.update',
            'penerimaan.delete',

            'pengeluaran.view',
            'pengeluaran.create',
            'pengeluaran.update',
            'pengeluaran.delete',

            'supplier.view',
            'supplier.create',
            'supplier.update',
            'supplier.delete',

            'laporan.view',

            'user.view',
            'user.create',
            'user.update',

            'cabang.view',
            'cabang.manage',
        ]);

        // 3. ADMIN CABANG: mengelola operasional cabangnya
        $adminCabang = Role::firstOrCreate([
            'name' => 'Admin Cabang',
            'guard_name' => 'web',
        ]);

        $adminCabang->syncPermissions([
            'barang.view',
            'barang.create',
            'barang.update',
            'barang.delete',

            'stok.view',
            'stok.update',

            'penerimaan.view',
            'penerimaan.create',
            'penerimaan.update',

            'pengeluaran.view',
            'pengeluaran.create',
            'pengeluaran.update',

            'supplier.view',
            'supplier.create',
            'supplier.update',

            'laporan.view',
        ]);

        // 4. PETUGAS GUDANG: aktivitas gudang
        $petugas = Role::firstOrCreate([
            'name' => 'Petugas Gudang',
            'guard_name' => 'web',
        ]);

        $petugas->syncPermissions([
            'barang.view',
            'stok.view',
            'penerimaan.view',
            'penerimaan.create',
            'pengeluaran.view',
            'pengeluaran.create',
        ]);

        // 5. VIEWER: hanya melihat data
        $viewer = Role::firstOrCreate([
            'name' => 'Viewer',
            'guard_name' => 'web',
        ]);

        $viewer->syncPermissions([
            'barang.view',
            'stok.view',
            'penerimaan.view',
            'pengeluaran.view',
            'supplier.view',
            'laporan.view',
        ]);

        // Bersihkan cache kembali
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}