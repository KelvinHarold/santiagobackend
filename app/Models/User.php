<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
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
}
