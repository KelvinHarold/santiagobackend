<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Delete all users
        User::query()->delete();

        // Reset auto increment
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@santiago.com',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'revenue_percentage' => 0,
        ]);

        // ✅ FIX: ensure role exists in sanctum guard
        $role = Role::findByName('Admin', 'sanctum');

        // ✅ Assign role properly
        $admin->assignRole($role);

        $this->command->info('Admin user created successfully!');
    }
}