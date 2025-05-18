<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Device;

class EmployeesController extends Controller
{
    // Add a new employee
    public function add(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'emp_id' => 'required|string|unique:employees_db,emp_id',
                'address' => 'required|string',
                'account_number' => 'required|string',
                'token' => 'nullable|string',
                'site_name' => 'nullable|string',
                'face_metadata' => 'nullable|string',
                'aadhar_card' => 'required|string',
                'mobile_number' => 'required|string',
                'shift_id' => 'required|exists:shifts_db,id', // ✅ validate shift
            ]);

            $token = $request->header('device_token');
            $platform = $request->header('platform');

            if (!$platform || !in_array($platform, ['android', 'ios', 'web'])) {
                return response()->json(['error' => 'Platform must be android, ios, or web'], 200);
            }

            if ($token) {
                $device = Device::where('token', $token)->first();
                if (!$device) {
                    return response()->json(['error' => 'Invalid token provided'], 200);
                }
            }

            $employee = Employee::create([
                'name' => $request->name,
                'emp_id' => $request->emp_id,
                'address' => $request->address,
                'account_number' => $request->account_number,
                'token' => $token,
                'site_name' => $request->site_name,
                'face_metadata' => $request->face_metadata,
                'aadhar_card' => $request->aadhar_card,
                'mobile_number' => $request->mobile_number,
                'shift_id' => $request->shift_id, // ✅ assign shift
            ]);

            return response()->json(['message' => 'Employee added successfully', 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error while adding employee: ' . $e->getMessage()], 200);
        }
    }


    public function getFaceEmbeddings()
    {
        $embeddings = Employee::select('emp_id', 'name', 'face_metadata')->get();
        return response()->json(['message' => 'Face embeddings fetched successfully', 'data' => $embeddings], 200);
    }


    // Fetch a single employee with shift details
public function show($id)
{
    try {
        $employee = Employee::with('shift')->find($id);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $data = [
            'id' => $employee->id,
            'name' => $employee->name,
            'emp_id' => $employee->emp_id,
            'address' => $employee->address,
            'account_number' => $employee->account_number,
            'token' => $employee->token,
            'site_name' => $employee->site_name,
            'location' => $employee->location,
            'face_metadata' => $employee->face_metadata,
            'clock_in' => $employee->clock_in,
            'clock_out' => $employee->clock_out,
            'aadhar_card' => $employee->aadhar_card,
            'mobile_number' => $employee->mobile_number,
            'shift' => $employee->shift ? [
                'id' => $employee->shift->id,
                'clock_in' => $employee->shift->clock_in,
                'clock_out' => $employee->shift->clock_out,
                'clock_in_window' => json_decode($employee->shift->clock_in_window),
                'clock_out_window' => json_decode($employee->shift->clock_out_window),
            ] : null,
        ];

        return response()->json(['message' => 'Employee fetched successfully', 'employee' => $data], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error fetching employee: ' . $e->getMessage()], 500);
    }
}
    
public function getEmployeeSiteRadius($employeeId)
{
    try {
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        if (!$employee->site_name) {
            return response()->json(['error' => 'Site name not set for this employee'], 400);
        }

        $site = \App\Models\Site::where('name', $employee->site_name)->first();

        if (!$site) {
            return response()->json(['error' => 'Site not found'], 404);
        }

        return response()->json([
            'message' => 'Site radius and location retrieved successfully',
            'site' => [
                'site_name' => $site->name,
                'radius' => $site->radius,
                'location' => $site->location,
            ]
        
        ], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error retrieving site data: ' . $e->getMessage()], 500);
    }
}



public function update(Request $request, $id)
{
    try {
        $platform = $request->header('platform');
        $token = $request->header('device_token');
        $employee = Employee::find($id);

        // Platform validation
        if (!$platform || !in_array($platform, ['android', 'ios', 'web'])) {
            return response()->json(['error' => 'Platform must be android, ios, or web'], 200);
        }

        // Check if employee exists
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        // Validation rules: make all fields nullable
        $request->validate([
            'name' => 'nullable|string',
            'emp_id' => 'nullable|string|unique:employees_db,emp_id,' . $employee->id,
            'address' => 'nullable|string',
            'account_number' => 'nullable|string',
            'aadhar_card' => 'nullable|string',
            'mobile_number' => 'nullable|string',
            'shift_id' => 'nullable|exists:shifts_db,id',
            'site_name' => 'nullable|string',
        ]);

        // Update only the fields that are present in the request
        $employee->update(array_filter($request->only([
            'name',
            'emp_id',
            'address',
            'account_number',
            'aadhar_card',
            'mobile_number',
            'shift_id',
            'site_name'
        ]))); 

        return response()->json(['message' => 'Employee updated successfully', 'status' => true], 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Error while updating employee: ' . $e->getMessage()], 200);
    }
}



    
    public function updateAttendanceTime(Request $request, $id)
    {
        $request->validate([
            'clock_in' => 'nullable|date_format:Y-m-d H:i:s',
            'clock_out' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $employee = Employee::find($id);
    
        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 404);
        }
    
        if ($request->filled('clock_in')) {
            $employee->clock_in = $request->input('clock_in');
        }
    
        if ($request->filled('clock_out')) {
            $employee->clock_out = $request->input('clock_out');
        }
    
        $employee->save();
    
        return response()->json([
            'message' => 'Attendance time updated successfully',
            'employee' => $employee
        ], 200);
    }
    
    

    // Fetch all employees
    public function index()
    {
        $employees = Employee::with('shift')->get();
    
        $data = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'emp_id' => $employee->emp_id,
                'address' => $employee->address,
                'account_number' => $employee->account_number,
                'token' => $employee->token,
                'site_name' => $employee->site_name,
                'location' => $employee->location,
                'face_metadata' => $employee->face_metadata,
                'clock_in' => $employee->clock_in,
                'clock_out' => $employee->clock_out,
                'aadhar_card' => $employee->aadhar_card,
                'mobile_number' => $employee->mobile_number,
                'shift' => $employee->shift ? [
                    'id' => $employee->shift->id,
                    'clock_in' => $employee->shift->clock_in,
                    'clock_out' => $employee->shift->clock_out,
                    'clock_in_window' => json_decode($employee->shift->clock_in_window),
                    'clock_out_window' => json_decode($employee->shift->clock_out_window),
                ] : null,
            ];
        });
    
        return response()->json([
            'message' => 'All employees fetched successfully',
            'employees' => $data,
        ], 200);
    }
    

    // Delete an employee
    public function delete($id)
    {
        try {
            $platform = request()->header('platform');
            $employee = Employee::find($id);

            if (!$platform) {
                return response()->json(['error' => 'Platform is required'], 200);
            }
            if ($platform != 'android' && $platform != 'ios' && $platform != 'web') {
                return response()->json(['error' => 'Platform must be either android or ios'], 200);
            }

            if (!$employee) {
                return response()->json(['error' => 'Employee not found'], 200);
            }

            $employee->delete();
            return response()->json(['message' => 'Employee deleted successfully'], 200);
                
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the employee: ' . $e->getMessage()], 200);
        }

    }
}
