<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RevenueShare;

class RevenueSharesSeeder extends Seeder
{
    public function run()
    {
        RevenueShare::create([
            'daily_report_id' => 1,
            'user_id' => 1,
            'percentage' => 60,
            'amount' => 300000,
        ]);

    }
}
