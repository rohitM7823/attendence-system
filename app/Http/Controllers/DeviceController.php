<?php

namespace App\Http\Controllers;
use App\Models\Device;
use App\Models\Employee;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class DeviceController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Get device headers
            $deviceName = $request->header('device_name');
            $platform = $request->header('platform');
            $deviceOsVersion = $request->header('device_os_version');
            $deviceModel = $request->header('device_model');
            $deviceRealId = $request->header('real_id');
            $deviceId = $request->header('device_id');

            if (!$deviceId) {
                return response()->json(['error' => 'Unauthorized device'], 200);
            }
            if(!$platform) {
                return response()->json(['error' => 'Platform is required'], 200);
            }
            if (!$deviceModel) {
                return response()->json(['error' => 'Device model is required'], 200);
            }
            if (!$deviceOsVersion) {
                return response()->json(['error' => 'Device OS version is required'], 200);
            }
            if (!$deviceRealId) {
                return response()->json(['error' => 'Device real ID is required'], 200);
            }
            if (!$deviceName) {
                return response()->json(['error' => 'Device name is required'], 200);
            }

            $existingDevice = Device::where('device_id', $deviceId)->first();
            if ($existingDevice) {
                return response()->json([
                    'message' => 'Device already registered',
                    'device_token' => $existingDevice->token
                ], 200);
            }

            // Generate a unique device identifier (UUID)
            $deviceIdentifier = Str::uuid()->toString();

            // Store the device in the database
            $device = Device::create([
                'name' => $deviceName,
                'os_version' => $deviceOsVersion,
                'platform' => $platform,
                'model' => $deviceModel,
                'read_id' => $deviceRealId,
                'status' => null, // Default status (it can later be updated)
                'device_id' => $deviceId,
                'token' => $deviceIdentifier,
                'emp_id' => null, // Optional, can be updated later if needed
            ]);

            // Return the unique token of the registered device
            return response()->json(['device_token' => $deviceIdentifier], 200);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Device Registration Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error '.$e->getMessage()], 200);
        }
    }

    public function approvedDevices()
    {
        try {
            $platform = request()->header('platform');

            if (!$platform) {
                return response()->json(['error' => 'Platform is required'], 200);
            }

            if (!in_array($platform, ['android', 'ios', 'web'])) {
                return response()->json(['error' => 'Platform must be either android, ios or web'], 200);
            }

            $devices = Device::where('status', 'Approved')->get();

            return response()->json([
                'message' => 'Approved devices fetched successfully',
                'devices' => $devices
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred while fetching approved devices: ' . $e->getMessage()
            ], 200);
        }
    }


    public function deviceStatus(Request $request)
    {
        try {
            // Retrieve the device token from the request
            $deviceToken = $request->header('device_token');
            $platform = $request->header('platform');
            if(!$platform) {
                return response()->json(['error' => 'Platform is required'], 200);
            }

            if($platform != 'android' && $platform != 'ios') {
                return response()->json(['error' => 'Platform must be either android or ios'], 200);
            }

            // Validate that the token exists
            if (!$deviceToken) {
                return response()->json(['error' => 'Device token is required'], 200);
            }

            // Find the device using the token
            $device = Device::where('token', $deviceToken)->first();

            // If the device doesn't exist, return an error
            if (!$device) {
                return response()->json(['error' => 'Device is not registered!'], 200);
            }

            // Return the status of the device
            return response()->json(['status' => $device->status], 200);

        } catch (\Exception $e) {
            // Log any error that occurs
            Log::error('Failed to fetch device status: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 200);
        }
    }

     // Fetch all registered devices
     public function index()
     {
         $devices = Device::all();
         return response()->json(['message' => 'All devices are fectched from', 'devices' => $devices], 200);
     }
 
     // Approve or Reject a device
     public function updateStatus(Request $request)
    {
        try {
            // Retrieve the device token and new status from the request
            $deviceToken = $request->header('device_token');  // Assuming the token is sent in headers
            $newStatus = $request->input('status');  // Status will be passed as part of the request body
            $platform = $request->header('platform');

            if(!$platform) {
                return response()->json(['error' => 'Platform is required'], 200);
            }

            if($platform != 'android' && $platform != 'ios' && $platform != 'web') {
                return response()->json(['error' => 'Platform must be either android or ios'], 200);
            }

            // Validate that the token and status exist
            if (!$deviceToken) {
                return response()->json(['error' => 'Device token is required'], 200);
            }

            if (!$newStatus || !in_array($newStatus, ['Approved', 'Rejected'])) {
                return response()->json(['error' => 'Valid status (Approved or Rejected) is required'], 200);
            }

            // Find the device using the token
            $device = Device::where('token', $deviceToken)->first();

            // If the device doesn't exist, return an error
            if (!$device) {
                return response()->json(['error' => 'Device not found'], 200);
            }

            // Update the status of the device
            $device->status = $newStatus;
            $device->save();

            // Return a success message with the updated status
            return response()->json(['message' => 'Device status updated successfully', 'status' => $device->status], 200);

        } catch (\Exception $e) {
            // Log any error that occurs
            Log::error('Failed to update device status: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error ' . $e->getMessage()], 500);
        }
    }

    public function deleteAllDevices()
    {
        try {
            $platform = request()->header('platform');

            if (!$platform) {
                return response()->json(['error' => 'Platform is required'], 200);
            }
            if ($platform != 'android' && $platform != 'ios' && $platform != 'web') {
                return response()->json(['error' => 'Platform must be either android or ios'], 200);
            }

            // First delete employees linked with devices
            Employee::whereNotNull('token')->delete();

            // Then delete all devices
            Device::query()->delete();

            // Optionally reset auto-increment
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE devices_db AUTO_INCREMENT = 1');

            return response()->json(['message' => 'All devices deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting devices: ' . $e->getMessage()], 200);
        }
    }

}
