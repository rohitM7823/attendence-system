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
                'designation' => 'required|string',
                'salary' => 'required|numeric',
                'token' => 'nullable|string',
                'site_name' => 'nullable|string',
                'face_metadata' => 'nullable|string',
            ]);
    
            $token = request()->header('device_token');
            $platform = request()->header('platform');

            if (!$platform) {
                return response()->json(['error' => 'Platform is required'], 200);
            }
            if ($platform != 'android' && $platform != 'ios' && $platform != 'web') {
                return response()->json(['error' => 'Platform must be either android or ios'], 200);
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
                'designation' => $request->designation,
                'salary' => $request->salary,
                'token' => $token, // token can be null
                'site_name' => $request->site_name,
                'face_metadata' => $request->face_metadata, 
            ]);
    
            return response()->json(['message' => 'Employee added successfully', 'status' => true], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while adding the employee: ' . $e->getMessage()], 200);
        }
    
    }

    public function getFaceEmbeddings()
    {
        $embeddings = Employee::select('emp_id', 'name', 'face_metadata')->get();
        return response()->json(['message' => 'Face embeddings fetched successfully', 'data' => $embeddings], 200);
    }


    // Update an existing employee
    public function update(Request $request, $id)
    {
        try {
            $platform = request()->header('platform');
            $token = request()->header('device_token');
            $employee = Employee::find($id);

            if (!$platform) {
                return response()->json(['error' => 'Platform is required'], 200);
            }
            if ($platform != 'android' && $platform != 'ios' && $platform != 'web') {
                return response()->json(['error' => 'Platform must be either android or ios'], 200);
            }    

            if (!$employee) {
                return response()->json(['error' => 'Employee not found'], 404);
            }
    
            $request->validate([
                'name' => 'sometimes|required|string',
                'emp_id' => 'sometimes|required|string|unique:employees_db,emp_id,' . $employee->id,
                'address' => 'sometimes|required|string',
                'designation' => 'sometimes|required|string',
                'salary' => 'sometimes|required|numeric',
                //'token' => 'nullable|string',
            ]);
    
        
    
            if ($token) {
                $device = Device::where('token', $token)->first();
                if (!$device) {
                    return response()->json(['error' => 'Invalid token provided'], 200);
                }
                $employee->token = $token;
            }
    
            $employee->update($request->only(['name', 'emp_id', 'address', 'designation', 'salary']));
    
            return response()->json(['message' => 'Employee updated successfully', 'employee' => $employee], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the employee: ' . $e->getMessage()], 200);
        }
    }

    // Fetch all employees
    public function index()
    {
        $employees = Employee::all();
        return response()->json(['message' => 'All employees are fetched from database', 'employees' => $employees], 200);
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

            $device = Device::where('token', $employee->token)->first();
            if ($device) {
                $device->delete();
            }

            $employee->delete();
            return response()->json(['message' => 'Employee deleted successfully'], 200);
                
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the employee: ' . $e->getMessage()], 200);
        }

    }
}
