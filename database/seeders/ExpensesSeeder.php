<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;

class ExpensesSeeder extends Seeder
{
    public function run()
    {
        Expense::create([
            'daily_report_id' => 1,
            'title' => 'Electricity Bill',
            'amount' => 50000,
        ]);

        Expense::create([
            'daily_report_id' => 2,
            'title' => 'Water Bill',
            'amount' => 30000,
        ]);
    }
}
