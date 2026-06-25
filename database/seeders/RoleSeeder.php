<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Admin',
            'Msimamizi',
            'Msaidizi',
            'Office',
            'Manager'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'sanctum' // ✅ FIXED
            ]);
        }

        $this->command->info('Roles seeded successfully (sanctum guard).');
    }
}