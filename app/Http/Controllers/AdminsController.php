<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AdminsController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'admin_id' => 'required',
                'password' => 'required',
            ]);

            $admin = Admin::where('admin_id', $request->admin_id)->first();

            if (!$admin || !Hash::check($request->password, $admin->password)) {
                throw ValidationException::withMessages([
                    'admin_id' => ['Invalid credentials.'],
                ]);
            }

            $token = $admin->createToken('admin-token')->plainTextToken;

            return response()->json(['token' => $token], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error during login'], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'admin_id' => 'required',
                'new_password' => 'required|min:8',
            ]);

            $admin = Admin::where('admin_id', $request->admin_id)->first();

            if (!$admin) {
                return response()->json(['error' => 'Admin not found'], 404);
            }

            $admin->password = bcrypt($request->new_password);
            $admin->save();

            return response()->json(['message' => 'Password updated successfully']);
        } catch (\Exception $e) {
            Log::error('Forgot Password Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error during password update'], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error during logout'], 500);
        }
    }
}
