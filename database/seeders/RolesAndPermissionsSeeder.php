<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view users', 'create users', 'edit users', 'delete users',
            'view categories', 'manage categories',
            'view products', 'manage products',
            'view ingredients', 'manage ingredients',
            'view recipes', 'manage recipes', 'log wastage',
            'open shift', 'close shift', 'view shifts', 'view all shifts',
            'create orders', 'view orders', 'update order status', 'cancel orders', 'view all orders',
            'process payments',
            'view expenses', 'manage expenses',
            'view reports', 'export reports',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'view categories', 'manage categories',
            'view products', 'manage products',
            'view ingredients', 'manage ingredients',
            'view recipes', 'manage recipes', 'log wastage',
            'open shift', 'close shift', 'view shifts', 'view all shifts',
            'create orders', 'view orders', 'update order status', 'cancel orders', 'view all orders',
            'process payments',
            'view expenses', 'manage expenses',
            'view reports', 'export reports',
        ]);

        $cashier = Role::firstOrCreate(['name' => 'cashier']);
        $cashier->syncPermissions([
            'open shift', 'close shift', 'view shifts',
            'create orders', 'view orders', 'update order status', 'cancel orders',
            'process payments',
        ]);

        $waiter = Role::firstOrCreate(['name' => 'waiter']);
        $waiter->syncPermissions([
            'create orders', 'view orders', 'update order status',
        ]);
    }
}
