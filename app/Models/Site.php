<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $table = 'site_db';

    protected $fillable = [
        'name',
        'location',
        'radius',
    ];

    protected $casts = [
        'location' => 'array',
    ];
}
