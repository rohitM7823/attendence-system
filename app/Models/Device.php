<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $table = 'devices_db';

    protected $fillable = [
        'name',
        'os_version',
        'platform',
        'model',
        'read_id',
        'status',
        'device_id',
        'token', // <-- Add this
        'emp_id',
    ];
    
    
    public function employee()
    {
        return $this->hasOne(Employee::class, 'token', 'token');
    }
}
