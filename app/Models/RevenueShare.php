<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class RevenueShare extends Model
{
    protected $fillable = [
        'daily_report_id',
        'user_id',
        'percentage',
        'amount'
    ];

  public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

public function dailyReport()
{
    return $this->belongsTo(DailyReport::class, 'daily_report_id');
}

}
