<?php

namespace App\Http\Controllers;

use App\Models\RevenueShare;
use App\Models\User;

class RevenueShareController extends Controller
{
    public function index()
    {
        return response()->json(RevenueShare::with(['user', 'dailyReport'])->get());
    }

// app/Http/Controllers/RevenueShareController.php
public function officeTotal()
{
    $officeUserIds = \App\Models\User::where('role', 'Office')->pluck('id');

    $totalOfficeAmount = \App\Models\RevenueShare::whereIn('user_id', $officeUserIds)->sum('amount');

    return response()->json(['total_office_amount' => $totalOfficeAmount]);
}

    
}
