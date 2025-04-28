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
        'designation',
        'salary',
        'token', // Foreign key
    ];

    // Define the relationship with the Device model
    public function device()
    {
        return $this->belongsTo(Device::class, 'token', 'token');
    }
}
