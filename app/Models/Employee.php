<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees_db'; // Define the table name

    protected $fillable = [
        'name',
        'emp_id',
        'address',
        'salary',
        'token',
        'site_name',
        'location',
        'face_metadata',
        'clock_in',
        'clock_out',
        'aadhar_card',
        'mobile_number',
    ];

    protected $casts = [
        'location' => 'array',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
    ];    

    // Define the relationship with the Device model
    public function device()
    {
        return $this->belongsTo(Device::class, 'token', 'token');
    }
}
