<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
    // Get all departments
    public function index()
    {
        try {
            $departments = Department::all();
            return response()->json([
                'message' => 'Departments fetched successfully',
                'departments' => $departments
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to fetch departments: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch departments'], 500);
        }
    }

    // Create a new department
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:departments,name',
            ]);

            $department = Department::create([
                'name' => $request->name
            ]);

            return response()->json([
                'message' => 'Department created successfully',
                'department' => $department
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create department: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create department'], 500);
        }
    }

    // Update a department
    public function update(Request $request, $id)
    {
        try {
            $department = Department::find($id);

            if (!$department) {
                return response()->json(['error' => 'Department not found'], 404);
            }

            $request->validate([
                'name' => 'required|string|unique:departments,name,' . $id,
            ]);

            $department->update([
                'name' => $request->name
            ]);

            return response()->json([
                'message' => 'Department updated successfully',
                'department' => $department
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update department: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update department'], 500);
        }
    }

    // Delete a department
    public function destroy($id)
    {
        try {
            $department = Department::find($id);

            if (!$department) {
                return response()->json(['error' => 'Department not found'], 404);
            }

            $department->delete();

            return response()->json(['message' => 'Department deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Failed to delete department: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete department'], 500);
        }
    }
}
