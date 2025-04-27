<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FaceController extends Controller
{
    public function getEmbeddings(Request $request)
    {
        try {
            $deviceName = $request->header('device_name');
            $platform = $request->header('platform');
            $deviceOsVersion = $request->header('device_os_version');
            $deviceModel = $request->header('device_model');
            $deviceRealId = $request->header('real_id');
            $deviceId = $request->header('device_id');

            if (!$deviceId) {
                return response()->json(['error' => 'Unauthorized device'], 401);
            }

    
            if(!$deviceModel) {
                return response()->json(['error' => 'Device model is required'], 400);
            }
            if(!$deviceOsVersion) {
                return response()->json(['error' => 'Device OS version is required'], 400);
            }
            if(!$deviceRealId) {
                return response()->json(['error' => 'Device real ID is required'], 400);
            }
            if(!$deviceName) {
                return response()->json(['error' => 'Device name is required'], 400);
            }


            // Mock response for now. Ideally, fetch real embeddings from DB or file.
            $embeddings = [
                0.123, 0.456, 0.789, 0.101, 0.112, 0.131, 0.415, 0.161, 0.718, 0.192,
                0.202, 0.212, 0.222, 0.232, 0.242, 0.252, 0.262, 0.272, 0.282, 0.292,
            ];

            return response()->json($embeddings, 200);
        } catch (\Exception $e) {
            Log::error('Fetching Face Embeddings Failed: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
