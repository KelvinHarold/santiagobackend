<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed all the required data
        $this->call([
             RoleSeeder::class,
            PermissionSeeder::class,
            UsersSeeder::class,
            DailyReportsSeeder::class,
            AttendancesSeeder::class,
            ExpensesSeeder::class,
            RevenueSharesSeeder::class,
        ]);
    }
}
