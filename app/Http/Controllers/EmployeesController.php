<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Device;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;

class EmployeesController extends Controller
{

    // Generate attendance report in PDF based on selected date range
    public function downloadAttendanceReportPdf(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $dateRange = collect();

            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dateRange->push($date->copy());
            }

            $employees = \App\Models\Employee::with('shift')->get();
            $reportData = [];

            foreach ($employees as $employee) {
                $dailyStatus = [];

                foreach ($dateRange as $date) {
                    $status = 'A'; // Default to Absent

                    $shift = $employee->shift;
                    if (!$shift || !$employee->clock_in || !$employee->clock_out) {
                        $dailyStatus[] = $status;
                        continue;
                    }

                    $clockInWindow = collect(json_decode($shift->clock_in_window, true));
                    $clockOutWindow = collect(json_decode($shift->clock_out_window, true));

                    $clockInWindowStart = Carbon::parse($clockInWindow['start'])->setDate($date->year, $date->month, $date->day);
                    $clockInWindowEnd = Carbon::parse($clockInWindow['end'])->setDate($date->year, $date->month, $date->day);
                    $clockOutWindowStart = Carbon::parse($clockOutWindow['start'])->setDate($date->year, $date->month, $date->day);
                    $clockOutWindowEnd = Carbon::parse($clockOutWindow['end'])->setDate($date->year, $date->month, $date->day);

                    $empClockIn = Carbon::parse($employee->clock_in);
                    $empClockOut = Carbon::parse($employee->clock_out);

                    if (
                        $empClockIn->between($clockInWindowStart, $clockInWindowEnd) &&
                        $empClockOut->between($clockOutWindowStart, $clockOutWindowEnd) &&
                        $empClockIn->isSameDay($date) &&
                        $empClockOut->isSameDay($date)
                    ) {
                        $status = 'P';
                    }

                    $dailyStatus[] = $status;
                }

                $reportData[] = [
                    'emp_id' => $employee->emp_id,
                    'name' => $employee->name,
                    'status' => $dailyStatus,
                ];
            }

            // ✅ Reference the Blade view `attendance.report`
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('attendance.report', [
                'reportData' => $reportData,
                'dateRange' => $dateRange->map(fn($d) => $d->format('Y-m-d')),
                'start' => $startDate->toFormattedDateString(),
                'end' => $endDate->toFormattedDateString()
            ])->setPaper('a4', 'landscape');

            $filename = 'attendance_report_' . now()->format('Ymd_His') . '.pdf';
            \Illuminate\Support\Facades\Storage::disk('app/public')->put($filename, $pdf->output());

            $url = asset('storage/' . $filename);

            return response()->json([
                'message' => 'Attendance PDF generated successfully.',
                'download_url' => $url
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation error',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate PDF',
                'message' => $e->getMessage()
            ], 500);
        }
    }



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
                'department_id' => 'nullable|exists:departments,id',
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
                'department_id' => $request->department_id,
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
        $employee = Employee::with('shift', 'department')->findOrFail($id);

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
                'department' => $employee->department ? [
                'id' => $employee->department->id,
                'name' => $employee->department->name,
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
            'department_id' => 'nullable|exists:departments,id',
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
            'site_name',
            'department_id',
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
    public function index(Request $request)
    {
        $query = Employee::with('shift', 'department');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                ->orWhere('emp_id', 'like', "%$search%")
                ->orWhere('mobile_number', 'like', "%$search%");
            });
        }

        // Pagination
        $perPage = $request->get('limit', 10); // default 10 per page
        $employees = $query->paginate($perPage);

        // Format data
        $data = $employees->getCollection()->map(function ($employee) {
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
                'department' => $employee->department ? [
                    'id' => $employee->department->id,
                    'name' => $employee->department->name,
                ] : null,
            ];
        });

        return response()->json([
            'message' => 'Employees fetched successfully',
            'employees' => $data,
            'pagination' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ],
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
