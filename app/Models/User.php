<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'cnic',
        'email',
        'password',
        'role',
         'voter_key',
        'has_voted',
    ];

    protected $hidden = [
        'password',
    ];
}

