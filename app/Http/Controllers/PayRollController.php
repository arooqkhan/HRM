<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\PayRoll;
use App\Models\Employee;
use Barryvdh\DomPDF\PDF;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PayRollController extends Controller
{

    protected $pdf;

    public function __construct(PDF $pdf = null)
    {
        if ($pdf) {
            $this->pdf = $pdf;
        }

        $this->middleware('permission:view payroll', ['only' => ['index']]);
        $this->middleware('permission:create payroll', ['only' => ['create', 'store']]);
        $this->middleware('permission:update payroll', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete payroll', ['only' => ['destroy']]);
        $this->middleware('permission:show payroll', ['only' => ['show']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'admin' || $user->role === 'HR' || $user->role === 'Accountant') {
            // Admin can see all payrolls
            $payrolls = PayRoll::with('employee')->orderBy('created_at', 'desc')->get();
        } else {
            // Employee can see only their own payrolls
            $payrolls = PayRoll::with('employee')
                ->where('employee_id', $user->employee_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('admin.pages.payroll.index', compact('payrolls'));
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

        return view('admin.pages.payroll.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary' => 'required|numeric',
            'bonus' => 'nullable|numeric',
            'deduction' => 'nullable|numeric',
            'total' => 'required|numeric',
        ]);

        // Create a new payroll entry
        $payroll = Payroll::create($validated);

        // Redirect to a specific route or return a response
        return redirect()->route('payroll.index')->with('success', 'Payroll entry created successfully!');
    }

    /**
     * Display the specified resource.
     */


    // public function show($id)
    // {

    //     $payroll = Payroll::findOrFail($id);
    //     $employee = $payroll->employee;

    //     // Fetch attendances for this employee for the current month
    //     $attendances = Attendance::where('employee_id', $employee->id)
    //         ->whereMonth('clock_in_date', Carbon::now()->month)
    //         ->whereYear('clock_in_date', Carbon::now()->year)
    //         ->get();

    //     // Initialize counters and date range
    //     $absentDaysCount = 0;
    //     $startDate = Carbon::now()->startOfMonth();
    //     $currentDate = Carbon::now(); // Current date
    //     $totalWorkingDays = 0;
    //     $totalWorkMinutes = 0;

    //     // Loop through each day of the month up to the current date
    //     while ($startDate <= $currentDate) {
    //         $dayOfWeek = $startDate->format('l');
    //         if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
    //             $totalWorkingDays++;
    //             $found = $attendances->contains('clock_in_date', $startDate->toDateString());
    //             if (!$found) {
    //                 $absentDaysCount++;
    //             }
    //         }
    //         $startDate->addDay();
    //     }

    //     // Calculate the total number of working days in the current month
    //     $totalDaysInMonth = $currentDate->daysInMonth;
    //     $workingDaysInMonth = 0;

    //     // Loop through each day of the month to count the working days
    //     for ($day = 1; $day <= $totalDaysInMonth; $day++) {
    //         $date = Carbon::createFromDate($currentDate->year, $currentDate->month, $day);
    //         $dayOfWeek = $date->format('l');
    //         if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
    //             $workingDaysInMonth++;
    //         }
    //     }

    //     // Calculate per day pay based on total working days in the current month
    //     $perDayPay = $workingDaysInMonth > 0 ? $payroll->total / $workingDaysInMonth : 0;
    //     $perHour = $perDayPay / 8;

    //     // Fetch paid leaves for the current month
    //     $paidLeaves = Leave::where('employee_id', $employee->id)
    //         ->where('status', 1)
    //         ->where(function ($query) {
    //             $query->whereMonth('date', Carbon::now()->month)
    //                 ->whereYear('date', Carbon::now()->year)
    //                 ->orWhere(function ($query) {
    //                     $query->whereMonth('start_date', Carbon::now()->month)
    //                         ->whereYear('start_date', Carbon::now()->year);
    //                 })
    //                 ->orWhere(function ($query) {
    //                     $query->whereMonth('end_date', Carbon::now()->month)
    //                         ->whereYear('end_date', Carbon::now()->year);
    //                 });
    //         })
    //         ->get();

    //     // Calculate the number of paid leave days excluding weekends
    //     $paidLeavesCount = $paidLeaves->sum('leave_days');

    //     // Calculate total working hours in the current month excluding weekends
    //     $totalHours = $workingDaysInMonth * 8;

    //     // Calculate total actual work hours and minutes
    //     foreach ($attendances as $attendance) {
    //         if ($attendance->clock_in_time && $attendance->clock_out_time) {
    //             $clockIn = Carbon::parse($attendance->clock_in_time);
    //             $clockOut = Carbon::parse($attendance->clock_out_time);
    //             $totalWorkMinutes += $clockOut->diffInMinutes($clockIn);
    //         }
    //     }

    //     // Convert total work minutes to hours and minutes
    //     $totalActualWorkHours = intdiv($totalWorkMinutes, 60);
    //     $totalActualWorkMinutes = $totalWorkMinutes % 60;

    //     // Calculate total expected work minutes
    //     $attendanceDaysCount = $attendances->count() * 8; // Total expected work hours
    //     $totalExpectedWorkMinutes = $attendanceDaysCount * 60;

    //     // Calculate deduction total minutes
    //     $deductionTotalMinutes = $totalExpectedWorkMinutes - $totalWorkMinutes;

    //     // Convert deduction minutes to hours and minutes, ensuring non-negative values
    //     $fullDeductionHours = max(0, intdiv($deductionTotalMinutes, 60));
    //     $fullDeductionMinutes = max(0, $deductionTotalMinutes % 60);

    //     // Store full deduction as an array
    //     $fullDeduction = [
    //         'hours' => $fullDeductionHours,
    //         'minutes' => $fullDeductionMinutes,
    //     ];

    //       // Calculate the deduction hour pay


    //       $deductionHourPay = ($fullDeductionHours + $fullDeductionMinutes / 60) * $perHour;

    //     // Find consignment time
    //     $totalMinutes = 0;

    //     // Get the attendance records where clock_in_time is after 10:15
    //     $attendanceRecords = Attendance::where('employee_id', $employee->id)
    //         ->where('clock_in_time', '>', Carbon::createFromTime(10, 15))
    //         ->whereMonth('clock_in_date', Carbon::now()->month)
    //         ->whereYear('clock_in_date', Carbon::now()->year)
    //         ->get();

    //     foreach ($attendanceRecords as $record) {
    //         // Calculate the difference in minutes from 10:15
    //         $clockInTime = Carbon::parse($record->clock_in_time);
    //         $referenceTime = Carbon::createFromTime(10, 15);
    //         $differenceInMinutes = $clockInTime->diffInMinutes($referenceTime);

    //         // Add the difference to the total minutes
    //         $totalMinutes += $differenceInMinutes;
    //     }

    //     $count = $totalMinutes;

    //     // Subtract the count from deductionTotalMinutes and ensure the result is non-negative
    //     $deductionTotalMinutes = max(0, $deductionTotalMinutes - $count);

    //     // Convert deduction minutes to hours and minutes
    //     $deductionHours = intdiv($deductionTotalMinutes, 60);
    //     $deductionMinutes = $deductionTotalMinutes % 60;
    //     $deductionDecimalHours = $deductionHours + ($count / 60);








    //     // Handle count reaching 60 minutes
    //     if ($count >= 60) {
    //         $deductionHours += intdiv($count, 60);
    //         $count %= 60;
    //     }

    //     // Calculate overtime
    //     $totalWorkHoursInMinutes = $attendanceDaysCount * 60;
    //     $totalActualWorkMinuteis = ($totalActualWorkHours * 60) + $totalActualWorkMinutes;

    //     $overtimeMinutes = $totalActualWorkMinuteis - $totalWorkHoursInMinutes;
    //     $overtimeHours = intdiv($overtimeMinutes, 60);
    //     $overtimeRemainingMinutes = $overtimeMinutes % 60;

    //     // Ensure overtime is non-negative
    //     $overtimeHours = max(0, $overtimeHours);
    //     $overtimeRemainingMinutes = max(0, $overtimeRemainingMinutes);


    //     // Calculate total expected work minutes (attendanceDaysCount * 60 minutes)
    //     $expectedWorkMinutes = $attendanceDaysCount * 60;

    //     // Calculate overtime in minutes
    //     $overtimeMinutes = $totalWorkMinutes > $expectedWorkMinutes ? $totalWorkMinutes - $expectedWorkMinutes : 0;

    //     $overTimePay = $overtimeMinutes * ($perHour / 60);




    //     return view('admin.pages.payroll.show', compact(
    //         'payroll',
    //         'absentDaysCount',
    //         'perDayPay',
    //         'paidLeavesCount',
    //         'totalWorkingDays',
    //         'totalHours',
    //         'totalActualWorkHours',
    //         'totalActualWorkMinutes',
    //         'attendances',
    //         'attendanceDaysCount',
    //         'deductionHours',
    //         'deductionMinutes',
    //         'deductionHourPay',
    //         'perHour',
    //         'count',
    //         'overtimeHours',
    //         'overtimeRemainingMinutes',
    //         'overtimeMinutes',
    //         'overTimePay',
    //         'fullDeduction'
    //     ));
    // }

    public function show($id)
    {

        // Check if the request is coming from the employee's index (using employee_id)
        $payroll = Payroll::where('employee_id', $id)->first();

        // If no payroll record is found using employee_id, treat the $id as payroll_id
        if (!$payroll) {
            $payroll = Payroll::findOrFail($id); // This assumes $id is the payroll_id
        }

        // Fetch the employee associated with the payroll
        $employee = $payroll->employee;

        // Fetch attendances for this employee for the current month
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('clock_in_date', Carbon::now()->month)
            ->whereYear('clock_in_date', Carbon::now()->year)
            ->get();

        // Initialize counters and date range
        $absentDaysCount = 0;
        $startDate = Carbon::now()->startOfMonth();
        $currentDate = Carbon::now(); // Current date
        $totalWorkingDays = 0;
        $totalWorkMinutes = 0;

        // Loop through each day of the month up to the current date
        while ($startDate <= $currentDate) {
            $dayOfWeek = $startDate->format('l');
            if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
                $totalWorkingDays++;
                $found = $attendances->contains('clock_in_date', $startDate->toDateString());
                if (!$found) {
                    $absentDaysCount++;
                }
            }
            $startDate->addDay();
        }

        // Calculate the total number of working days in the current month
        $totalDaysInMonth = $currentDate->daysInMonth;
        $workingDaysInMonth = 0;

        // Loop through each day of the month to count the working days
        for ($day = 1; $day <= $totalDaysInMonth; $day++) {
            $date = Carbon::createFromDate($currentDate->year, $currentDate->month, $day);
            $dayOfWeek = $date->format('l');
            if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
                $workingDaysInMonth++;
            }
        }

        // Calculate per day pay based on total working days in the current month
        $perDayPay = $workingDaysInMonth > 0 ? $payroll->total / $workingDaysInMonth : 0;
        $perHour = $perDayPay / 8;

        // Fetch paid leaves for the current month
        $paidLeaves = Leave::where('employee_id', $employee->id)
            ->where('status', 1)
            ->where(function ($query) {
                $query->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year)
                    ->orWhere(function ($query) {
                        $query->whereMonth('start_date', Carbon::now()->month)
                            ->whereYear('start_date', Carbon::now()->year);
                    })
                    ->orWhere(function ($query) {
                        $query->whereMonth('end_date', Carbon::now()->month)
                            ->whereYear('end_date', Carbon::now()->year);
                    });
            })
            ->get();

        // Calculate the number of paid leave days excluding weekends
        $paidLeavesCount = $paidLeaves->sum('leave_days');

        // Calculate total working hours in the current month excluding weekends
        $totalHours = $workingDaysInMonth * 8;

        // Calculate total actual work hours and minutes
        foreach ($attendances as $attendance) {
            if ($attendance->clock_in_time && $attendance->clock_out_time) {
                $clockIn = Carbon::parse($attendance->clock_in_time);
                $clockOut = Carbon::parse($attendance->clock_out_time);
                $totalWorkMinutes += $clockOut->diffInMinutes($clockIn);
            }
        }

        // Convert total work minutes to hours and minutes
        $totalActualWorkHours = intdiv($totalWorkMinutes, 60);
        $totalActualWorkMinutes = $totalWorkMinutes % 60;

        // Calculate total expected work minutes
        $attendanceDaysCount = $attendances->count() * 8; // Total expected work hours
        $totalExpectedWorkMinutes = $attendanceDaysCount * 60;

        // Calculate deduction total minutes
        $deductionTotalMinutes = $totalExpectedWorkMinutes - $totalWorkMinutes;

        // Convert deduction minutes to hours and minutes, ensuring non-negative values
        $fullDeductionHours = max(0, intdiv($deductionTotalMinutes, 60));
        $fullDeductionMinutes = max(0, $deductionTotalMinutes % 60);

        // Store full deduction as an array
        $fullDeduction = [
            'hours' => $fullDeductionHours,
            'minutes' => $fullDeductionMinutes,
        ];

        // Calculate the deduction hour pay


        $deductionHourPay = ($fullDeductionHours + $fullDeductionMinutes / 60) * $perHour;

        // Find consignment time
        $totalMinutes = 0;

        // Get the attendance records where clock_in_time is after 10:15
        $attendanceRecords = Attendance::where('employee_id', $employee->id)
            ->where('clock_in_time', '>', Carbon::createFromTime(10, 15))
            ->whereMonth('clock_in_date', Carbon::now()->month)
            ->whereYear('clock_in_date', Carbon::now()->year)
            ->get();

        foreach ($attendanceRecords as $record) {
            // Calculate the difference in minutes from 10:15
            $clockInTime = Carbon::parse($record->clock_in_time);
            $referenceTime = Carbon::createFromTime(10, 15);
            $differenceInMinutes = $clockInTime->diffInMinutes($referenceTime);

            // Add the difference to the total minutes
            $totalMinutes += $differenceInMinutes;
        }

        $count = $totalMinutes;

        // Subtract the count from deductionTotalMinutes and ensure the result is non-negative
        $deductionTotalMinutes = max(0, $deductionTotalMinutes - $count);

        // Convert deduction minutes to hours and minutes
        $deductionHours = intdiv($deductionTotalMinutes, 60);
        $deductionMinutes = $deductionTotalMinutes % 60;
        $deductionDecimalHours = $deductionHours + ($count / 60);








        // Handle count reaching 60 minutes
        if ($count >= 60) {
            $deductionHours += intdiv($count, 60);
            $count %= 60;
        }

        // Calculate overtime
        $totalWorkHoursInMinutes = $attendanceDaysCount * 60;
        $totalActualWorkMinuteis = ($totalActualWorkHours * 60) + $totalActualWorkMinutes;

        $overtimeMinutes = $totalActualWorkMinuteis - $totalWorkHoursInMinutes;
        $overtimeHours = intdiv($overtimeMinutes, 60);
        $overtimeRemainingMinutes = $overtimeMinutes % 60;

        // Ensure overtime is non-negative
        $overtimeHours = max(0, $overtimeHours);
        $overtimeRemainingMinutes = max(0, $overtimeRemainingMinutes);


        // Calculate total expected work minutes (attendanceDaysCount * 60 minutes)
        $expectedWorkMinutes = $attendanceDaysCount * 60;

        // Calculate overtime in minutes
        $overtimeMinutes = $totalWorkMinutes > $expectedWorkMinutes ? $totalWorkMinutes - $expectedWorkMinutes : 0;

        $overTimePay = $overtimeMinutes * ($perHour / 60);




        return view('admin.pages.payroll.show', compact(
            'payroll',
            'absentDaysCount',
            'perDayPay',
            'paidLeavesCount',
            'totalWorkingDays',
            'totalHours',
            'totalActualWorkHours',
            'totalActualWorkMinutes',
            'attendances',
            'attendanceDaysCount',
            'deductionHours',
            'deductionMinutes',
            'deductionHourPay',
            'perHour',
            'count',
            'overtimeHours',
            'overtimeRemainingMinutes',
            'overtimeMinutes',
            'overTimePay',
            'fullDeduction'
        ));
    }

















    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $payroll = Payroll::findOrFail($id);
        $employees = Employee::all();

        return view('admin.pages.payroll.edit', compact('payroll', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'salary' => 'required|numeric',
            'bonus' => 'nullable|numeric',
            'deduction' => 'nullable|numeric',
            'total' => 'required|numeric',
        ]);

        $payroll = Payroll::findOrFail($id);
        $payroll->update($validated);

        return redirect()->route('payroll.index')->with('success', 'Payroll entry updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $payroll = PayRoll::findOrFail($id);

        $payroll->delete();

        return redirect()->route('payroll.index')->with('success', 'PayRoll Delete successfully');
    }

    public function download($id)
    {
        $payroll = Payroll::findOrFail($id);
        $employee = $payroll->employee;

        // Fetch attendances for this employee for the current month
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('clock_in_date', Carbon::now()->month)
            ->whereYear('clock_in_date', Carbon::now()->year)
            ->get();

        // Initialize counters and date range
        $absentDaysCount = 0;
        $startDate = Carbon::now()->startOfMonth();
        $currentDate = Carbon::now(); // Current date
        $totalWorkingDays = 0;
        $totalWorkMinutes = 0;

        // Loop through each day of the month up to the current date
        while ($startDate <= $currentDate) {
            $dayOfWeek = $startDate->format('l');
            if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
                $totalWorkingDays++;
                $found = $attendances->contains('clock_in_date', $startDate->toDateString());
                if (!$found) {
                    $absentDaysCount++;
                }
            }
            $startDate->addDay();
        }

        // Calculate the total number of working days in the current month
        $totalDaysInMonth = $currentDate->daysInMonth;
        $workingDaysInMonth = 0;

        // Loop through each day of the month to count the working days
        for ($day = 1; $day <= $totalDaysInMonth; $day++) {
            $date = Carbon::createFromDate($currentDate->year, $currentDate->month, $day);
            $dayOfWeek = $date->format('l');
            if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
                $workingDaysInMonth++;
            }
        }

        // Calculate per day pay based on total working days in the current month
        $perDayPay = $workingDaysInMonth > 0 ? $payroll->total / $workingDaysInMonth : 0;
        $perHour = $perDayPay / 8;

        // Fetch paid leaves for the current month
        $paidLeaves = Leave::where('employee_id', $employee->id)
            ->where('status', 1)
            ->where(function ($query) {
                $query->whereMonth('date', Carbon::now()->month)
                    ->whereYear('date', Carbon::now()->year)
                    ->orWhere(function ($query) {
                        $query->whereMonth('start_date', Carbon::now()->month)
                            ->whereYear('start_date', Carbon::now()->year);
                    })
                    ->orWhere(function ($query) {
                        $query->whereMonth('end_date', Carbon::now()->month)
                            ->whereYear('end_date', Carbon::now()->year);
                    });
            })
            ->get();

        // Calculate the number of paid leave days excluding weekends
        $paidLeavesCount = $paidLeaves->sum('leave_days');

        // Calculate total working hours in the current month excluding weekends
        $totalHours = $workingDaysInMonth * 8;

        // Calculate total actual work hours and minutes
        foreach ($attendances as $attendance) {
            if ($attendance->clock_in_time && $attendance->clock_out_time) {
                $clockIn = Carbon::parse($attendance->clock_in_time);
                $clockOut = Carbon::parse($attendance->clock_out_time);
                $totalWorkMinutes += $clockOut->diffInMinutes($clockIn);
            }
        }

        // Convert total work minutes to hours and minutes
        $totalActualWorkHours = intdiv($totalWorkMinutes, 60);
        $totalActualWorkMinutes = $totalWorkMinutes % 60;

        // Calculate total expected work minutes
        $attendanceDaysCount = $attendances->count() * 8; // Total expected work hours
        $totalExpectedWorkMinutes = $attendanceDaysCount * 60;

        // Calculate deduction total minutes
        $deductionTotalMinutes = $totalExpectedWorkMinutes - $totalWorkMinutes;

        // Convert deduction minutes to hours and minutes, ensuring non-negative values
        $fullDeductionHours = max(0, intdiv($deductionTotalMinutes, 60));
        $fullDeductionMinutes = max(0, $deductionTotalMinutes % 60);

        // Store full deduction as an array
        $fullDeduction = [
            'hours' => $fullDeductionHours,
            'minutes' => $fullDeductionMinutes,
        ];

        // Find consignment time
        $totalMinutes = 0;

        // Get the attendance records where clock_in_time is after 10:15
        $attendanceRecords = Attendance::where('employee_id', $employee->id)
            ->where('clock_in_time', '>', Carbon::createFromTime(10, 15))
            ->whereMonth('clock_in_date', Carbon::now()->month)
            ->whereYear('clock_in_date', Carbon::now()->year)
            ->get();

        foreach ($attendanceRecords as $record) {
            // Calculate the difference in minutes from 10:15
            $clockInTime = Carbon::parse($record->clock_in_time);
            $referenceTime = Carbon::createFromTime(10, 15);
            $differenceInMinutes = $clockInTime->diffInMinutes($referenceTime);

            // Add the difference to the total minutes
            $totalMinutes += $differenceInMinutes;
        }

        $count = $totalMinutes;

        // Subtract the count from deductionTotalMinutes and ensure the result is non-negative
        $deductionTotalMinutes = max(0, $deductionTotalMinutes - $count);

        // Convert deduction minutes to hours and minutes
        $deductionHours = intdiv($deductionTotalMinutes, 60);
        $deductionMinutes = $deductionTotalMinutes % 60;
        $deductionDecimalHours = $deductionHours + ($count / 60);

        $deductionDecimalHours = $fullDeduction['hours'] + ($fullDeduction['minutes'] / 60);

        // Calculate the deduction hour pay

        $deductionHourPay = $deductionDecimalHours * $perHour;



        // Handle count reaching 60 minutes
        if ($count >= 60) {
            $deductionHours += intdiv($count, 60);
            $count %= 60;
        }

        // Calculate overtime
        $totalWorkHoursInMinutes = $attendanceDaysCount * 60;
        $totalActualWorkMinuteis = ($totalActualWorkHours * 60) + $totalActualWorkMinutes;

        $overtimeMinutes = $totalActualWorkMinuteis - $totalWorkHoursInMinutes;
        $overtimeHours = intdiv($overtimeMinutes, 60);
        $overtimeRemainingMinutes = $overtimeMinutes % 60;

        // Ensure overtime is non-negative
        $overtimeHours = max(0, $overtimeHours);
        $overtimeRemainingMinutes = max(0, $overtimeRemainingMinutes);

        // Calculate total expected work minutes (attendanceDaysCount * 60 minutes)
        $expectedWorkMinutes = $attendanceDaysCount * 60;

        // Calculate overtime in minutes
        $overtimeMinutes = $totalWorkMinutes > $expectedWorkMinutes ? $totalWorkMinutes - $expectedWorkMinutes : 0;

        $overTimePay = $overtimeMinutes * ($perHour / 60);

        // Prepare data for the PDF
        $data = [
            'payroll' => $payroll,
            'absentDaysCount' => $absentDaysCount,
            'perDayPay' => $perDayPay,
            'paidLeavesCount' => $paidLeavesCount,
            'totalWorkingDays' => $totalWorkingDays,
            'totalHours' => $totalHours,
            'totalActualWorkHours' => $totalActualWorkHours,
            'totalActualWorkMinutes' => $totalActualWorkMinutes,
            'attendances' => $attendances,
            'attendanceDaysCount' => $attendanceDaysCount,
            'deductionHours' => $deductionHours,
            'deductionMinutes' => $deductionMinutes,
            'deductionHourPay' => $deductionHourPay,
            'perHour' => $perHour,
            'count' => $count,
            'overtimeHours' => $overtimeHours,
            'overtimeRemainingMinutes' => $overtimeRemainingMinutes,
            'overtimeMinutes' => $overtimeMinutes,
            'overTimePay' => $overTimePay,
            'fullDeduction' => $fullDeduction
        ];

        // Load the view and generate the PDF
        $pdf = $this->pdf->loadView('admin.pages.payroll.pdf', $data);

        return $pdf->download('payroll_' . $employee->first_name . '_' . $employee->last_name . '.pdf');
    }


    // public function download($id)
    // {

    //     $payroll = Payroll::findOrFail($id);
    //     $employee = $payroll->employee;

    //     // Fetch attendances for this employee for the current month
    //     $attendances = Attendance::where('employee_id', $employee->id)
    //         ->whereMonth('clock_in_date', Carbon::now()->month)
    //         ->whereYear('clock_in_date', Carbon::now()->year)
    //         ->get();

    //     // Initialize counters and date range
    //     $absentDaysCount = 0;
    //     $startDate = Carbon::now()->startOfMonth();
    //     $currentDate = Carbon::now(); // Current date
    //     $totalWorkingDays = 0;
    //     $totalWorkMinutes = 0;

    //     // Loop through each day of the month up to the current date
    //     while ($startDate <= $currentDate) {
    //         $dayOfWeek = $startDate->format('l');
    //         if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
    //             $totalWorkingDays++;
    //             $found = $attendances->contains('clock_in_date', $startDate->toDateString());
    //             if (!$found) {
    //                 $absentDaysCount++;
    //             }
    //         }
    //         $startDate->addDay();
    //     }

    //     // Calculate the total number of working days in the current month
    //     $totalDaysInMonth = $currentDate->daysInMonth;
    //     $workingDaysInMonth = 0;

    //     // Loop through each day of the month to count the working days
    //     for ($day = 1; $day <= $totalDaysInMonth; $day++) {
    //         $date = Carbon::createFromDate($currentDate->year, $currentDate->month, $day);
    //         $dayOfWeek = $date->format('l');
    //         if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
    //             $workingDaysInMonth++;
    //         }
    //     }

    //     // Calculate per day pay based on total working days in the current month
    //     $perDayPay = $workingDaysInMonth > 0 ? $payroll->total / $workingDaysInMonth : 0;
    //     $perHour = $perDayPay / 8;

    //     // Fetch paid leaves for the current month
    //     $paidLeaves = Leave::where('employee_id', $employee->id)
    //         ->where('status', 1)
    //         ->where(function ($query) {
    //             $query->whereMonth('date', Carbon::now()->month)
    //                 ->whereYear('date', Carbon::now()->year)
    //                 ->orWhere(function ($query) {
    //                     $query->whereMonth('start_date', Carbon::now()->month)
    //                         ->whereYear('start_date', Carbon::now()->year);
    //                 })
    //                 ->orWhere(function ($query) {
    //                     $query->whereMonth('end_date', Carbon::now()->month)
    //                         ->whereYear('end_date', Carbon::now()->year);
    //                 });
    //         })
    //         ->get();

    //     // Calculate the number of paid leave days excluding weekends
    //     $paidLeavesCount = $paidLeaves->sum('leave_days');

    //     // Calculate total working hours in the current month excluding weekends
    //     $totalHours = $workingDaysInMonth * 8;

    //     // Calculate total actual work hours and minutes
    //     foreach ($attendances as $attendance) {
    //         if ($attendance->clock_in_time && $attendance->clock_out_time) {
    //             $clockIn = Carbon::parse($attendance->clock_in_time);
    //             $clockOut = Carbon::parse($attendance->clock_out_time);
    //             $totalWorkMinutes += $clockOut->diffInMinutes($clockIn);
    //         }
    //     }

    //     // Convert total work minutes to hours and minutes
    //     $totalActualWorkHours = intdiv($totalWorkMinutes, 60);
    //     $totalActualWorkMinutes = $totalWorkMinutes % 60;


    //     // Calculate deduction work hours and minutes
    //     $attendanceDaysCount = $attendances->count() * 8; // Convert hours to minutes
    //     $deductionTotalMinutes = ($attendanceDaysCount * 60) - $totalWorkMinutes;

    //     // Find consignment time
    //     $totalMinutes = 0;

    //     // Get the attendance records where clock_in_time is after 10:15
    //     $attendanceRecords = Attendance::where('employee_id', $employee->id)
    //         ->where('clock_in_time', '>', Carbon::createFromTime(10, 15))
    //         ->whereMonth('clock_in_date', Carbon::now()->month)
    //         ->whereYear('clock_in_date', Carbon::now()->year)
    //         ->get();

    //     foreach ($attendanceRecords as $record) {
    //         // Calculate the difference in minutes from 10:15
    //         $clockInTime = Carbon::parse($record->clock_in_time);
    //         $referenceTime = Carbon::createFromTime(10, 15);
    //         $differenceInMinutes = $clockInTime->diffInMinutes($referenceTime);

    //         // Add the difference to the total minutes
    //         $totalMinutes += $differenceInMinutes;
    //     }

    //     $count = $totalMinutes;

    //     // Subtract the count from deductionTotalMinutes and ensure the result is non-negative
    //     $deductionTotalMinutes = max(0, $deductionTotalMinutes - $count);

    //     // Convert deduction minutes to hours and minutes
    //     $deductionHours = intdiv($deductionTotalMinutes, 60);
    //     $deductionMinutes = $deductionTotalMinutes % 60;
    //     $deductionDecimalHours = $deductionHours + ($count / 60);
    //     $deductionHourPay = $deductionDecimalHours * $perHour;

    //     // Handle count reaching 60 minutes
    //     if ($count >= 60) {
    //         $deductionHours += intdiv($count, 60);
    //         $count %= 60;
    //     }


    //     // Calculate overtime
    //     $totalWorkHoursInMinutes = $attendanceDaysCount * 60;
    //     $totalActualWorkMinuteis = ($totalActualWorkHours * 60) + $totalActualWorkMinutes;

    //     $overtimeMinutes = $totalActualWorkMinuteis - $totalWorkHoursInMinutes;
    //     $overtimeHours = intdiv($overtimeMinutes, 60);
    //     $overtimeRemainingMinutes = $overtimeMinutes % 60;

    //     // Ensure overtime is non-negative
    //     $overtimeHours = max(0, $overtimeHours);
    //     $overtimeRemainingMinutes = max(0, $overtimeRemainingMinutes);


    //     // Calculate total expected work minutes (attendanceDaysCount * 60 minutes)
    //     $expectedWorkMinutes = $attendanceDaysCount * 60;

    //     // Calculate overtime in minutes
    //     $overtimeMinutes = $totalWorkMinutes > $expectedWorkMinutes ? $totalWorkMinutes - $expectedWorkMinutes : 0;

    //     $overTimePay = $overtimeMinutes * ($perHour / 60);


    //     $data = [
    //         'payroll' => $payroll,
    //         'absentDaysCount' => $absentDaysCount,
    //         'perDayPay' => $perDayPay,
    //         'paidLeavesCount' => $paidLeavesCount,
    //         'totalWorkingDays' => $totalWorkingDays,
    //         'totalHours' => $totalHours,
    //         'totalActualWorkHours' => $totalActualWorkHours,
    //         'totalActualWorkMinutes' => $totalActualWorkMinutes,
    //         'attendances' => $attendances,
    //         'attendanceDaysCount' => $attendanceDaysCount,
    //         'deductionHours' => $deductionHours,
    //         'deductionMinutes' => $deductionMinutes,
    //         'deductionHourPay' => $deductionHourPay,
    //         'perHour' => $perHour,
    //         'count' => $count,
    //         'overtimeHours' => $overtimeHours,
    //         'overtimeRemainingMinutes' => $overtimeRemainingMinutes,
    //         'overtimeMinutes' => $overtimeMinutes,
    //         'overTimePay' => $overTimePay
    //     ];

    //     // Load the view and generate the PDF
    //     $pdf = $this->pdf->loadView('admin.pages.payroll.pdf', $data);

    //     return $pdf->download('payroll_' . $employee->first_name . '_' . $employee->last_name . '.pdf');
    // }
}
