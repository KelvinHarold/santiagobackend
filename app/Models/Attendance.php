<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'daily_report_id',
        'main_hall_people',
        'main_hall_staff',
        'vip_hall_people',
        'vip_hall_staff',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    public function getTotalAttribute()
    {
        return ($this->main_hall_people - $this->main_hall_staff)
             + ($this->vip_hall_people - $this->vip_hall_staff);
    }
}
