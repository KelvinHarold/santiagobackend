<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    protected $fillable = [
        'match_name',
        'report_date',
        'total_income',
        'remaining_balance',
        'uploaded_by',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function attendances()
    {
        return $this->hasOne(Attendance::class, 'daily_report_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'daily_report_id');
    }

    public function revenueShares()
    {
        return $this->hasMany(RevenueShare::class, 'daily_report_id');
    }

}
