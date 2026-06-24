<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'daily_report_id',
        'title',
        'amount'
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}
