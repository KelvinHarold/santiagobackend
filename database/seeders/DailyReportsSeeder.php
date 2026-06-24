<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DailyReport;
use Carbon\Carbon;

class DailyReportsSeeder extends Seeder
{
    public function run()
    {
        DailyReport::create([
            'match_name' => 'Sunday Service',
            'report_date' => Carbon::now(),
            'total_income' => 500000,
            'remaining_balance' => 150000,
        ]);

        DailyReport::create([
            'match_name' => 'Friday Prayer',
            'report_date' => Carbon::now()->subDay(),
            'total_income' => 300000,
            'remaining_balance' => 80000,
        ]);
    }
}
