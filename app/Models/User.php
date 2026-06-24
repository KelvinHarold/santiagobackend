<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, Notifiable;

      protected $guard_name = 'sanctum';

    protected $fillable = [
        'name',
        'email',
        'profile_picture',
        'password',
        'role',
        'revenue_percentage', // asilimia ya mgao
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function revenueShares()
    {
        return $this->hasMany(RevenueShare::class);
    }

    /**
     * Send the password reset notification to the frontend React SPA.
     * The link will be: FRONTEND_URL/reset-password?token=TOKEN&email=EMAIL
     */
    public function sendPasswordResetNotification($token)
    {
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

        // Override the reset URL to point to the React SPA
        ResetPasswordNotification::createUrlUsing(function ($notifiable, $token) use ($frontendUrl) {
            return $frontendUrl . '/reset-password?token=' . $token . '&email=' . urlencode($notifiable->email);
        });

        $this->notify(new ResetPasswordNotification($token));
    }
}
