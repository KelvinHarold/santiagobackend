<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;

class AttendancesSeeder extends Seeder
{
    public function run()
    {
        Attendance::create([
            'daily_report_id' => 1,
            'main_hall_people' => 200,
            'main_hall_staff' => 20,
            'vip_hall_people' => 50,
            'vip_hall_staff' => 5,
        ]);

        Attendance::create([
            'daily_report_id' => 2,
            'main_hall_people' => 150,
            'main_hall_staff' => 15,
            'vip_hall_people' => 40,
            'vip_hall_staff' => 4,
        ]);
    }
}
