<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'UserManagement',
            'Roles Management',
            'Permission Management',
            'ExpensesManagement',
            'ReportUpload',
            'ReportView',
        ];

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Remove obsolete permissions not in the new schema
        Permission::whereNotIn('name', $permissions)->delete();

        // Create new permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'sanctum']
            );
        }

        // Get roles
        $adminRole = Role::where('name', 'Admin')->first();
        $msimamiziRole = Role::where('name', 'Msimamizi')->first();
        $msaidiziRole = Role::where('name', 'Msaidizi')->first();

        // Admin gets all permissions
        if ($adminRole) {
            $adminRole->syncPermissions(Permission::all());
        }

        // Msimamizi/Msaidizi only get ReportUpload & ReportView
        $basicPermissions = Permission::whereIn('name', [
            'ReportUpload',
            'ReportView',
        ])->get();

        if ($msimamiziRole) {
            $msimamiziRole->syncPermissions($basicPermissions);
        }

        if ($msaidiziRole) {
            $msaidiziRole->syncPermissions($basicPermissions);
        }

        echo "✅ Permissions updated successfully.\n";
    }
}
