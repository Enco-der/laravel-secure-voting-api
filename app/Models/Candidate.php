<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'username',
        'password',
        'applicant_name',
        'email',
        'country',
        'organization',
        'applied_date',
        'region_code',
        'access_scope',
        'cnic_picture',
        'documents',
        'feedback'
    ];

    protected $casts = [
        'documents' => 'array'
    ];
}

