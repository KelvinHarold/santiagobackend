<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 🔥 Always clear cache first
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'UserManagement',
            'Roles Management',
            'Permission Management',
            'ExpensesManagement',
            'ReportUpload',
            'ReportView',
        ];

        // 1. Create permissions (force sanctum guard)
        foreach ($permissions as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'sanctum',
            ]);
        }

        // 2. Ensure Admin role exists (safe fallback)
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'sanctum',
        ]);

        // 3. Clear old permissions for clean reset
        $adminRole->permissions()->detach();

        // 4. Attach permissions safely (NO syncPermissions)
        $permissionIds = Permission::where('guard_name', 'sanctum')
            ->pluck('id')
            ->toArray();

        $adminRole->permissions()->attach($permissionIds);

        $this->command->info('✅ Permissions successfully linked to Admin role.');
    }
}