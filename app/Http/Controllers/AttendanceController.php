<?php

namespace App\Http\Controllers;

use Log;
use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use App\Models\Employee;
use Faker\Core\DateTime;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
     {
         $this->middleware('permission:view attendance', ['only' => ['index']]);
         $this->middleware('permission:create attendance', ['only' => ['create','store']]);
         $this->middleware('permission:update attendance', ['only' => ['update','edit']]);
         $this->middleware('permission:delete attendance', ['only' => ['destroy']]);
         $this->middleware('permission:show attendance', ['only' => ['show']]);
     }

     public function index()
     {
         $user = auth()->user();
     
         if ($user->role === 'admin' || $user->role === 'HR' || $user->role === 'Accountant') {
             // Admin can see all attendances
             $attendances = Attendance::with('employee')->orderBy('created_at', 'desc')->get();
         } else {
             // Employee can see only their own attendances
             $attendances = Attendance::with('employee')->where('employee_id', $user->employee_id)->orderBy('created_at', 'desc')->get();
         }
     
         return view('admin.pages.attendance.index', compact('attendances'));
     }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $user = Auth::user();
    
    $user = auth()->user(); // Get the currently authenticated user

    if ($user->role === 'admin') {
        // Admin sees all employees
        $employees = Employee::all(['id', 'first_name', 'last_name']);
    } else {
        // Non-admin sees only their own record
        $employees = Employee::where('id', $user->employee_id)
                             ->get(['id', 'first_name', 'last_name']);
    }

    return view('admin.pages.attendance.create', compact('employees'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'clock_in_date' => 'required|date',
            'clock_in_time' => 'required',
            'clock_out_date' => 'nullable|date',
            'clock_out_time' => 'nullable',
            'reason' => 'required',
        ]);

        // Create a new Attendance instance
        $attendance = new Attendance();
        $attendance->employee_id = $request->employee_id;
        $attendance->clock_in_date = $request->clock_in_date;
        $attendance->clock_in_time = $request->clock_in_time;
        $attendance->clock_out_date = $request->clock_out_date;
        $attendance->clock_out_time = $request->clock_out_time;
        $attendance->reason = $request->reason;

        // Save the attendance record
        $attendance->save();

        // Optionally, you can redirect back with a success message
        return redirect()->route('attendance.index')->with('success', 'Attendance recorded successfully.');
    }
    /**
     * Display the specified resource.
     */
    public function show($employee_id)
{
    
    // Fetch employee and their attendance records
    $employee = Employee::findOrFail($employee_id);

    $startOfMonth = Carbon::now()->startOfMonth();
    $currentDate = Carbon::now()->toDateString();

    // Fetch attendance records for the employee
    $attendances = Attendance::where('employee_id', $employee_id)
        ->whereBetween('clock_in_date', [$startOfMonth->toDateString(), $currentDate])
        ->orderBy('clock_in_date', 'desc')
        ->get();

        $abc = [];
        $monthName = $startOfMonth->format('F Y');

    return view('admin.pages.attendance.show', compact('employee', 'attendances','abc','monthName'));
}





public function monthlyDetails($employee_id)
{
    // Fetch employee details and monthly attendance
    $employee = Employee::findOrFail($employee_id);

    // Get the start of the month and the current date
    $startOfMonth = Carbon::now()->startOfMonth();
    $currentDate = Carbon::now()->toDateString();

    // Fetch attendances from the start of the month up to the current date
    $attendances = Attendance::where('employee_id', $employee_id)
        ->whereBetween('clock_in_date', [$startOfMonth->toDateString(), $currentDate])
        ->orderBy('clock_in_date', 'desc')
        ->get();

    $abc = []; // Initialize $abc as an empty array
    $monthName = $startOfMonth->format('F Y');

    return view('admin.pages.attendance.show', compact('employee', 'attendances', 'abc', 'monthName'));
}


public function showPreviousMonths($employee_id, $monthOffset = 1)
{
    // Get the current date
    $currentDate = Carbon::now();

    // Calculate the start and end date of the specific month based on the monthOffset
    $startDate = $currentDate->copy()->subMonths($monthOffset)->startOfMonth();
    $endDate = $currentDate->copy()->subMonths($monthOffset)->endOfMonth();

    // Retrieve the employee
    $employee = Employee::findOrFail($employee_id);

    // Retrieve the attendance records for the selected month
    $attendances = Attendance::where('employee_id', $employee_id)
        ->whereBetween('clock_in_date', [$startDate->toDateString(), $endDate->toDateString()])
        ->get();

    // Pass the data to the view, including the correct month and year
    return view('admin.pages.attendance.show', [
        'employee' => $employee,
        'attendances' => $attendances,
        'monthName' => $startDate->format('F Y'), // This will show the correct month in the heading
        'monthOffset' => $monthOffset
    ]);
}


public function attendanceDetailsMonthly(Request $request, $employee_id)
{
    // Get the month and year from the request or default to the current month and year
    $month = $request->get('month', date('n')); // Current month
    $year = $request->get('year', date('Y')); // Current year

    // Fetch attendance records for the specified employee, month, and year
    $attendances = Attendance::where('employee_id', $employee_id)
        ->whereYear('clock_in_date', $year)
        ->whereMonth('clock_in_date', $month)
        ->get();

    // Get employee details
    $employee = Employee::findOrFail($employee_id);
    // Format month name
    $monthName = Carbon::create()->month($month)->format('F');

    // Return the view with attendance records
    return view('admin.pages.attendance.show', compact('attendances', 'employee', 'monthName'));
}































    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $attendance = Attendance::findOrFail($id);

    $attendance->delete();

    return redirect()->route('attendance.index')->with('success', 'Recorded deleted successfully');
    }
}
