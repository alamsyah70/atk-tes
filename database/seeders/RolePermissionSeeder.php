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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | 1. DAFTAR SELURUH PERMISSION
        |--------------------------------------------------------------------------
        */

        $permissions = [
            // Dashboard
            'dashboard.view',

            // Master Barang
            'barang.view',
            'barang.create',
            'barang.update',
            'barang.delete',

            // Stok
            'stok.view',
            'stok.update',

            // Kartu Stok
            'stock-card.view',

            // Penerimaan
            'penerimaan.view',
            'penerimaan.create',
            'penerimaan.update',
            'penerimaan.delete',

            // Pengeluaran
            'pengeluaran.view',
            'pengeluaran.create',
            'pengeluaran.update',
            'pengeluaran.delete',

            // Supplier
            'supplier.view',
            'supplier.create',
            'supplier.update',
            'supplier.delete',

            // Stok Opname
            'stok-opname.view',
            'stok-opname.create',
            'stok-opname.update',
            'stok-opname.finalize',
            'stok-opname.cancel',

            // Laporan
            'laporan.view',
            'laporan.print',
            'laporan.export',

            // Manajemen User
            'user.view',
            'user.create',
            'user.update',
            'user.delete',

            // Cabang
            'cabang.view',
            'cabang.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. SUPER ADMIN
        | Seluruh permission dan lintas cabang
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        /*
        |--------------------------------------------------------------------------
        | 3. IT CABANG
        | Seluruh permission dan lintas cabang
        |--------------------------------------------------------------------------
        */

        $itCabang = Role::firstOrCreate([
            'name' => 'IT Cabang',
            'guard_name' => 'web',
        ]);

        $itCabang->syncPermissions(
            Permission::where('guard_name', 'web')->get()
        );

        /*
        |--------------------------------------------------------------------------
        | 4. ADMIN PUSAT
        | Permission lama dipertahankan
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | 5. ADMIN GERAI
        | CRUD operasional cabang sendiri
        |--------------------------------------------------------------------------
        */

        $adminGerai = Role::firstOrCreate([
            'name' => 'Admin Gerai',
            'guard_name' => 'web',
        ]);

        $adminGerai->syncPermissions([
            'dashboard.view',

            // Master Barang: CRUD
            'barang.view',
            'barang.create',
            'barang.update',
            'barang.delete',

            // Penerimaan: CRUD
            'penerimaan.view',
            'penerimaan.create',
            'penerimaan.update',
            'penerimaan.delete',

            // Pengeluaran: CRUD
            'pengeluaran.view',
            'pengeluaran.create',
            'pengeluaran.update',
            'pengeluaran.delete',

            // Kartu Stok
            'stok.view',
            'stock-card.view',

            // Supplier: CRUD
            'supplier.view',
            'supplier.create',
            'supplier.update',
            'supplier.delete',

            // Laporan
            'laporan.view',
            'laporan.print',
            'laporan.export',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 6. INVENTORY AUDITOR
        | Hanya melihat transaksi, tetapi boleh melakukan stok opname
        |--------------------------------------------------------------------------
        */

        $inventoryAuditor = Role::firstOrCreate([
            'name' => 'Inventory Auditor',
            'guard_name' => 'web',
        ]);

        $inventoryAuditor->syncPermissions([
            // Dashboard
            'dashboard.view',

            // Master Barang: lihat saja
            'barang.view',

            // Penerimaan: lihat saja
            'penerimaan.view',

            // Pengeluaran: lihat saja
            'pengeluaran.view',

            // Kartu Stok
            'stok.view',
            'stock-card.view',

            // Stok Opname
            'stok-opname.view',
            'stok-opname.create',
            'stok-opname.update',
            'stok-opname.finalize',

            // Laporan
            'laporan.view',
            'laporan.print',
            'laporan.export',
        ]);

        /*
        |--------------------------------------------------------------------------
        | 7. PETUGAS GUDANG
        | Permission lama dipertahankan
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | 8. VIEWER
        | Permission lama dipertahankan
        |--------------------------------------------------------------------------
        */

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

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
