<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shift;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function index()
{
    $shifts = Shift::all()->map(function ($shift) {
        return [
            'id' => $shift->id,
            'clock_in' => $shift->clock_in,
            'clock_out' => $shift->clock_out,
            'clock_in_window' => json_decode($shift->clock_in_window),
            'clock_out_window' => json_decode($shift->clock_out_window),
            'created_at' => $shift->created_at,
            'updated_at' => $shift->updated_at,
        ];
    });

    return response()->json([
        'message' => 'All defined shifts',
        'count' => $shifts->count(),
        'shifts' => $shifts
    ]);
}


    public function show($id)
    {
        $shift = Shift::find($id);
        if (!$shift) {
            return response()->json(['error' => 'Shift not found'], 404);
        }
        return response()->json($shift);
    }

    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'clock_in' => 'required|date',
        ]);

        $clockIn = Carbon::parse($request->clock_in);
        $clockOut = $clockIn->copy()->addHours(8);

        $shift = Shift::updateOrCreate(
            ['id' => $request->id], // if `id` exists, it updates; otherwise, creates new
            [
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
                'clock_in_window' => json_encode([
                    'start' => $clockIn->copy()->subMinutes(30)->toDateTimeString(),
                    'end' => $clockIn->copy()->addMinutes(30)->toDateTimeString(),
                ]),
                'clock_out_window' => json_encode([
                    'start' => $clockOut->copy()->subMinutes(120)->toDateTimeString(),
                    'end' => $clockOut->copy()->addMinutes(120)->toDateTimeString(),
                ]),
            ]
        );

        return response()->json($shift, $request->id ? 200 : 201);
    }
}
