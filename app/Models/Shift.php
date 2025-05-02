<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $table = 'shifts_db';

    protected $fillable = [
        'clock_in',
        'clock_out',
        'clock_in_window',
        'clock_out_window',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'clock_in_window' => 'array',
        'clock_out_window' => 'array',
    ];
}
