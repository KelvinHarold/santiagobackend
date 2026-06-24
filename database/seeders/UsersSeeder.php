<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        // Delete all users
        User::query()->delete();

        // Reset auto increment (MySQL)
        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@santiago.com',
            'password' => bcrypt('password'),
            'role' => 'Admin',
            'revenue_percentage' => 0,
        ]);

        $admin->syncRoles(['Admin']);

        $this->command->info('✅ Admin user created successfully!');
    }
}