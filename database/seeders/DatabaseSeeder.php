<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Departments
        $departments = [
            ['name' => 'IT', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HR', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Finance', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Operations', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marketing', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('departments')->insert($departments);

        // Seed Sites
        $sites = [
            [
                'name' => 'Main Office',
                'location' => json_encode(['lat' => 12.9716, 'lng' => 77.5946]),
                'radius' => 100,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Branch Office',
                'location' => json_encode(['lat' => 13.0827, 'lng' => 80.2707]),
                'radius' => 75,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];
        DB::table('site_db')->insert($sites);

        // // Seed Shifts
        // $shifts = [];
        
        // // Morning shift (9:00 AM)
        // $clockIn = Carbon::createFromTimeString('09:00:00');
        // $clockOut = $clockIn->copy()->addHours(8);
        // $shifts[] = [
        //     'clock_in' => $clockIn,
        //     'clock_out' => $clockOut,
        //     'clock_in_window' => (string)json_encode([
        //         'start' => $clockIn->copy()->subMinutes(30)->toDateTimeString(),
        //         'end' => $clockIn->copy()->addMinutes(30)->toDateTimeString(),
        //     ]),
        //     'clock_out_window' => (string)json_encode([
        //         'start' => $clockOut->copy()->subMinutes(120)->toDateTimeString(),
        //         'end' => $clockOut->copy()->addMinutes(120)->toDateTimeString(),
        //     ]),
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ];

        // // Afternoon shift (14:00 PM)
        // $clockIn = Carbon::createFromTimeString('14:00:00');
        // $clockOut = $clockIn->copy()->addHours(8);
        // $shifts[] = [
        //     'clock_in' => $clockIn,
        //     'clock_out' => $clockOut,
        //     'clock_in_window' => (string)json_encode([
        //         'start' => $clockIn->copy()->subMinutes(30)->toDateTimeString(),
        //         'end' => $clockIn->copy()->addMinutes(30)->toDateTimeString(),
        //     ]),
        //     'clock_out_window' => (string)json_encode([
        //         'start' => $clockOut->copy()->subMinutes(120)->toDateTimeString(),
        //         'end' => $clockOut->copy()->addMinutes(120)->toDateTimeString(),
        //     ]),
        //     'created_at' => now(),
        //     'updated_at' => now()
        // ];

        // DB::table('shifts_db')->insert($shifts);

        // // Seed Employees
        // $employees = [];
        // $departments = DB::table('departments')->pluck('id')->toArray();
        // $shifts = DB::table('shifts_db')->pluck('id')->toArray();

        // for ($i = 1; $i <= 10; $i++) {
        //     $employees[] = [
        //         'name' => "Employee $i",
        //         'emp_id' => "EMP" . str_pad($i, 4, '0', STR_PAD_LEFT),
        //         'address' => "Address for Employee $i",
        //         'department_id' => $departments[array_rand($departments)],
        //         'shift_id' => $shifts[array_rand($shifts)],
        //         'account_number' => "ACC" . str_pad($i, 10, '0', STR_PAD_LEFT),
        //         'mobile_number' => "98765" . str_pad($i, 5, '0', STR_PAD_LEFT),
        //         'aadhar_card' => "123456" . str_pad($i, 6, '0', STR_PAD_LEFT),
        //         'created_at' => now(),
        //         'updated_at' => now()
        //     ];
        // }
        // DB::table('employees_db')->insert($employees);

        // Seed Devices
        // $devices = [];
        // $employees = DB::table('employees_db')->pluck('id')->toArray();
        // $statuses = ['Approved', 'Rejected', null];

        // for ($i = 1; $i <= 5; $i++) {
        //     $devices[] = [
        //         'name' => "Device $i",
        //         'os_version' => "Android " . rand(10, 13),
        //         'platform' => "Android",
        //         'model' => "Model $i",
        //         'read_id' => "RID" . str_pad($i, 4, '0', STR_PAD_LEFT),
        //         'status' => $statuses[array_rand($statuses)],
        //         'device_id' => "DEV" . str_pad($i, 4, '0', STR_PAD_LEFT),
        //         'emp_id' => $employees[array_rand($employees)],
        //         'token' => "TKN" . str_pad($i, 8, '0', STR_PAD_LEFT),
        //         'created_at' => now(),
        //         'updated_at' => now()
        //     ];
        // }
        // DB::table('devices_db')->insert($devices);
    }
}
