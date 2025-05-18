<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees_db'; // Define the table name

    protected $fillable = [
        'name',
        'emp_id',
        'address',
        'account_number', // changed from 'salary'
        'token',
        'site_name',
        'location',
        'face_metadata',
        'clock_in',
        'clock_out',
        'aadhar_card',
        'mobile_number',
        'shift_id', // ✅ Add this
    ];

    protected $casts = [
        'location' => 'array',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'account_number' => 'string',
    ];    

    // Define the relationship with the Device model
    public function device()
    {
        return $this->belongsTo(Device::class, 'token', 'token');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

}
