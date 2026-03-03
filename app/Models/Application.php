<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'applicant_name',
        'email',
        'country',
        'organization',
        'applied_date',
        'status',
        'documents',
        'region_code',
        'access_scope',
        'feedback'
    ];

    // If you want Laravel to automatically cast JSON to array
    protected $casts = [
        'documents' => 'array',
        'applied_date' => 'date',
    ];
}
