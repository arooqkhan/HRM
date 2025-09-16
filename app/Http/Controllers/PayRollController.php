<?php

namespace App\Http\Controllers;

use Log;
use DateTime;
use Carbon\Carbon;
use App\Models\Leave;
use App\Models\PayRoll;
use App\Models\Employee;
use Barryvdh\DomPDF\PDF;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\PayslipUpload;
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

        if ($user->role === 'admin' ||$user->role === 'HR' || $user->role === 'Accountant') {
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





 

     public function show($payrollId, $employeeId, $firstName, $lastName)
     {
    
         // Get the current year and month
         $currentYear = Carbon::now()->year;
         $currentMonth = Carbon::now()->month;
     
         // Generate a list of months from January to the current month
         $months = [];
         for ($m = 1; $m <= $currentMonth; $m++) {
             $months[] = Carbon::createFromDate($currentYear, $m, 1)->format('F'); // Full month name
         }

         $employees = Employee::all();
     
         // Pass the data to the view
         return view('admin.pages.payroll.show', compact('employeeId', 'currentYear', 'months','employees','firstName','lastName'));
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



    public function generate(Request $request, $employeeId, $firstName, $lastName)
    {
        
        // Validate the form inputs
        $request->validate([
            'employee' => 'required|exists:employees,id',
            'month' => 'required',
        ]);
    
        // Fetch the selected employee based on their ID
        $employee = Employee::find($request->employee);
    
        // Fetch all payslip uploads
        $payslipUploads = PayslipUpload::all();
    
        // Initialize an empty array for matching PDFs
        $matchingPdfs = [];
    
        // Iterate through each payslip upload to decode the 'pdfs' field and check for matching files
        foreach ($payslipUploads as $payslipUpload) {
            // Decode the JSON data in the 'pdfs' column
            $pdfPaths = json_decode($payslipUpload->pdfs, true);
    
            // Check if the month matches the selected month
            if ($payslipUpload->month === $request->month) {
                // Loop through each PDF path to find matches by basename
                foreach ($pdfPaths as $pdfPath) {
                    $filename = basename($pdfPath);  // Extract just the filename (e.g., EMP01.pdf)
                    $employeeIdFromPdf = pathinfo($filename, PATHINFO_FILENAME); // Extract employee_id (e.g., EMP01)
    
                    // Check if the employee_id in the filename matches the current employee's ID
                    if ($employee->employee_id == $employeeIdFromPdf) {
                        // If it matches, add to matching PDFs array
                        $matchingPdfs[] = $pdfPath;
                    }
                }
            }
        }
    
        // Convert the numeric month to a name
        $monthName = DateTime::createFromFormat('!m', $request->month)->format('F');
    
        // Check if a search was performed and no PDFs matched
        if ($request->has('employee') && $request->has('month') && empty($matchingPdfs)) {
            $notFoundMessage = "No payslips found for the selected month of " . $monthName;
        }
    
        // Pass data to the view
        return view('admin.pages.payroll.show', [
            'employees' => Employee::all(),  // Pass the employee list for the dropdown
            'matchingPdfs' => $matchingPdfs, // Pass matching PDFs for display
            'notFoundMessage' => $notFoundMessage ?? null, // Pass not found message if applicable
            'employeeId' => $employeeId,      // Pass employeeId
            'firstName' => $firstName,        // Pass firstName
            'lastName' => $lastName,          // Pass lastName
            'selectedMonthName' => $monthName // Pass the selected month name directly
        ]);
    }
    

    
    
    
    
    
    







// ya show ka code hy us ka
    // public function show($id)
    // {


    //     // Check if the request is coming from the employee's index (using employee_id)
    //     $payroll = Payroll::where('employee_id', $id)->first();

    //     // If no payroll record is found using employee_id, treat the $id as payroll_id
    //     if (!$payroll) {
    //         $payroll = Payroll::findOrFail($id); // This assumes $id is the payroll_id
    //     }

    //     // Fetch the employee associated with the payroll
    //     $employee = $payroll->employee;

    //     // Fetch attendances for this employee for the current month
    //     $attendances = Attendance::where('employee_id', $employee->id)
    //         ->whereMonth('clock_in_date', Carbon::now()->month)
    //         ->whereYear('clock_in_date', Carbon::now()->year)
    //         ->get();

    //     // Initialize counters and date range
    //     $absentDaysCount = 0;
    //     $startDate = Carbon::now()->startOfMonth();
    //     $currentDate = Carbon::now();
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

    //     // Calculate the deduction hour pay


    //     $deductionHourPay = ($fullDeductionHours + $fullDeductionMinutes / 60) * $perHour;

    //     // Find consignment time
    //     $totalMinutes = 0;

   

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

    //     // Pass employeeId to the view
    //     $employeeId = $employee->id;


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
    //         'fullDeduction',
    //         'employeeId',
    //     ));
    // }

    // ya us month ko download karnay wala code hy
    // public function download($id, $month, $year)
    // {
    //     // Check if the request is coming from the employee's index (using employee_id)
    //     $payroll = Payroll::where('employee_id', $id)->first();
    
    //     // If no payroll record is found using employee_id, treat the $id as payroll_id
    //     if (!$payroll) {
    //         $payroll = Payroll::findOrFail($id); // This assumes $id is the payroll_id
    //     }
    
    //     // Fetch the employee associated with the payroll
    //     $employee = $payroll->employee;
    
    //     // Fetch attendances for this employee for the specified month and year
    //     $attendances = Attendance::where('employee_id', $employee->id)
    //         ->whereMonth('clock_in_date', $month)
    //         ->whereYear('clock_in_date', $year)
    //         ->get();
    
    //     // Initialize counters
    //     $absentDaysCount = 0;
    //     $totalWorkingDays = 0;
    
    //     // Start from the first day of the month to the last day of the month
    //     $startDate = Carbon::createFromDate($year, $month, 1);
    //     $endDate = Carbon::createFromDate($year, $month, $startDate->daysInMonth); // Last day of the month
    
    //     // Loop through each day of the month
    //     while ($startDate <= $endDate) {
    //         // Check if it's a weekday (Monday to Friday)
    //         if (!in_array($startDate->format('l'), ['Saturday', 'Sunday'])) {
    //             $totalWorkingDays++; // Count this as a working day
    
    //             // Check if the employee was present on this date
    //             $found = $attendances->contains('clock_in_date', $startDate->toDateString());
    //             if (!$found) {
    //                 $absentDaysCount++; // Increment absent days if no attendance record is found
    //             }
    //         }
    //         $startDate->addDay(); // Move to the next day
    //     }
    
    //     // Calculate total working days in the specified month
    //     $totalDaysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
    //     $workingDaysInMonth = 0;
    
    //     // Loop through each day of the month to count the working days
    //     for ($day = 1; $day <= $totalDaysInMonth; $day++) {
    //         $date = Carbon::createFromDate($year, $month, $day);
    //         $dayOfWeek = $date->format('l');
    //         if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
    //             $workingDaysInMonth++;
    //         }
    //     }
    
    //     // Calculate per day pay based on total working days in the specified month
    //     $perDayPay = $workingDaysInMonth > 0 ? $payroll->total / $workingDaysInMonth : 0;
    //     $perHour = $perDayPay / 8;
    
    //     // Fetch paid leaves for the specified month and year
    //     $paidLeaves = Leave::where('employee_id', $employee->id)
    //         ->where('status', 1)
    //         ->where(function ($query) use ($month, $year) {
    //             $query->whereMonth('date', $month)
    //                 ->whereYear('date', $year)
    //                 ->orWhere(function ($query) use ($month, $year) {
    //                     $query->whereMonth('start_date', $month)
    //                         ->whereYear('start_date', $year);
    //                 })
    //                 ->orWhere(function ($query) use ($month, $year) {
    //                     $query->whereMonth('end_date', $month)
    //                         ->whereYear('end_date', $year);
    //                 });
    //         })
    //         ->get();
    
    //     // Calculate the number of paid leave days excluding weekends
    //     $paidLeavesCount = $paidLeaves->sum('leave_days');
    
    //     // Calculate total working hours in the specified month excluding weekends
    //     $totalHours = $workingDaysInMonth * 8;
    
    //     // Calculate total actual work hours and minutes
    //     $totalWorkMinutes = 0; // Reset total work minutes
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
    
    //     // Format total actual work hours for display
    //     $formattedTotalActualWorkHours = sprintf('%d:%02d', $totalActualWorkHours, $totalActualWorkMinutes);
    
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
    
    //     // Calculate the deduction hour pay
    //     $deductionHourPay = ($fullDeductionHours + $fullDeductionMinutes / 60) * $perHour;
    
    //     // Calculate overtime
    //     $totalWorkHoursInMinutes = $attendanceDaysCount * 60;
    //     $totalActualWorkMinutes = ($totalActualWorkHours * 60) + $totalActualWorkMinutes;
    
    //     $overtimeMinutes = $totalActualWorkMinutes - $totalWorkHoursInMinutes;
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
    
    //     // Pass employeeId to the view
    //     $employeeId = $employee->id;
    
    //     // Return view with the payroll details
    //     return view('admin.pages.payroll.pdf', compact(
    //         'payroll',
    //         'paidLeavesCount',
    //         'absentDaysCount',
    //         'perDayPay',
    //         'totalHours',
    //         'formattedTotalActualWorkHours', // Use the formatted value here
    //         'deductionHourPay', // Total deduction amount
    //         'overtimeHours',
    //         'overtimeRemainingMinutes',
    //         'overTimePay'
    //     ));
    // }
    // ya jo month select karo us ka record sow karnay k leya

    // public function showMonth(Request $request)
    // {
    //     // Get employee ID, month, and year from request
    //     $id = $request->input('id');
    //     $month = $request->input('month') ?? Carbon::now()->month;
    //     $year = $request->input('year') ?? Carbon::now()->year;
    
    //     // Check if the request is coming from the employee's index (using employee_id)
    //     $payroll = Payroll::where('employee_id', $id)->first();
    
    //     // If no payroll record is found using employee_id, treat the $id as payroll_id
    //     if (!$payroll) {
    //         $payroll = Payroll::findOrFail($id); // This assumes $id is the payroll_id
    //     }
    
    //     // Fetch the employee associated with the payroll
    //     $employee = $payroll->employee;
    
    //     // Fetch attendances for this employee for the selected month and year
    //     $attendances = Attendance::where('employee_id', $employee->id)
    //         ->whereMonth('clock_in_date', $month)
    //         ->whereYear('clock_in_date', $year)
    //         ->get();
    
    //     // Initialize counters and date range for the selected month
    //     $absentDaysCount = 0;
    //     $totalWorkingDays = 0;
    
    //     // Define the start and end dates for the selected month
    //     $startDate = Carbon::createFromDate($year, $month, 1);
    //     $endDate = Carbon::createFromDate($year, $month, $startDate->daysInMonth);
    
    //     // Loop through each day of the month
    //     while ($startDate <= $endDate) {
    //         $dayOfWeek = $startDate->format('l');
    //         // Count only weekdays (Monday to Friday)
    //         if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
    //             $totalWorkingDays++;
    //             // Check if attendance exists for this day
    //             $found = $attendances->contains('clock_in_date', $startDate->toDateString());
    //             if (!$found) {
    //                 $absentDaysCount++;
    //             }
    //         }
    //         // Move to the next day
    //         $startDate->addDay();
    //     }
    
    //     // Calculate the total number of working days in the month
    //     $workingDaysInMonth = $totalWorkingDays; // Already counted in the previous loop
    
    //     // Calculate per day and per hour pay
    //     $perDayPay = $workingDaysInMonth > 0 ? $payroll->total / $workingDaysInMonth : 0;
    //     $perHour = $perDayPay / 8;
    
    //     // Total expected hours based on working days
    //     $totalHours = $workingDaysInMonth * 8;
    
    //     // Fetch paid leaves for the selected month
    //     $paidLeaves = Leave::where('employee_id', $employee->id)
    //         ->where('status', 1)
    //         ->where(function ($query) use ($month, $year) {
    //             $query->whereMonth('date', $month)
    //                 ->whereYear('date', $year)
    //                 ->orWhere(function ($query) use ($month, $year) {
    //                     $query->whereMonth('start_date', $month)
    //                         ->whereYear('start_date', $year);
    //                 })
    //                 ->orWhere(function ($query) use ($month, $year) {
    //                     $query->whereMonth('end_date', $month)
    //                         ->whereYear('end_date', $year);
    //                 });
    //         })
    //         ->get();
    
    //     // Calculate paid leaves count
    //     $paidLeavesCount = $paidLeaves->sum('leave_days');
    
    //     // Calculate total work minutes for actual attendances
    //     $totalWorkMinutes = $attendances->reduce(function ($carry, $attendance) {
    //         if ($attendance->clock_in_time && $attendance->clock_out_time) {
    //             $clockIn = Carbon::parse($attendance->clock_in_time);
    //             $clockOut = Carbon::parse($attendance->clock_out_time);
    //             return $carry + $clockOut->diffInMinutes($clockIn);
    //         }
    //         return $carry;
    //     }, 0);
    
    //     // Convert total work minutes to hours and minutes
    //     $totalActualWorkHours = intdiv($totalWorkMinutes, 60);
    //     $totalActualWorkMinutes = $totalWorkMinutes % 60;
    
    //     // Calculate expected work minutes for attendance days
    //     $attendanceDaysCount = $attendances->count() * 8; // Total expected work hours
    //     $totalExpectedWorkMinutes = $attendanceDaysCount * 60;
    
    //     // Calculate deduction total minutes
    //     $deductionTotalMinutes = max(0, $totalExpectedWorkMinutes - $totalWorkMinutes);
    
    //     // Convert deduction minutes to hours and minutes
    //     $fullDeductionHours = intdiv($deductionTotalMinutes, 60);
    //     $fullDeductionMinutes = $deductionTotalMinutes % 60;
    
    //     $fullDeduction = [
    //         'hours' => $fullDeductionHours,
    //         'minutes' => $fullDeductionMinutes,
    //     ];
    
    //     // Deduction hour pay calculation
    //     $deductionHourPay = ($fullDeductionHours + $fullDeductionMinutes / 60) * $perHour;
    
    //     // Calculate overtime
    //     $overtimeMinutes = max(0, $totalWorkMinutes - $totalExpectedWorkMinutes);
    //     $overtimeHours = intdiv($overtimeMinutes, 60);
    //     $overtimeRemainingMinutes = $overtimeMinutes % 60;
    
    //     // Overtime pay calculation
    //     $overTimePay = $overtimeMinutes * ($perHour / 60);
    //     $employeeId = $employee->id;
    
    //     // Return view with calculated data
    //     return view('admin.pages.payroll.show', compact(
    //         'payroll',
    //         'absentDaysCount',
    //         'perDayPay',
    //         'paidLeavesCount',
    //         'totalWorkingDays',
    //         'totalActualWorkHours',
    //         'totalActualWorkMinutes',
    //         'attendances',
    //         'attendanceDaysCount',
    //         'deductionHourPay',
    //         'perHour',
    //         'overtimeHours',
    //         'overtimeRemainingMinutes',
    //         'overtimeMinutes',
    //         'overTimePay',
    //         'fullDeduction',
    //         'employee',
    //         'totalHours',
    //         'employeeId'
    //     ));
    // }
    
}
