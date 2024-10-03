<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\PayRoll;
use App\Models\Employee;
use Barryvdh\DomPDF\PDF;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\PayslipUpload;

class PaySlipController extends Controller
{

    protected $pdf;

    public function __construct(PDF $pdf = null)
    {
        if ($pdf) {
            $this->pdf = $pdf;
        }

        $this->middleware('permission:view payslip', ['only' => ['index']]);
        $this->middleware('permission:create payslip', ['only' => ['create', 'store']]);
        $this->middleware('permission:update payslip', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete payslip', ['only' => ['destroy']]);
        $this->middleware('permission:show payslip', ['only' => ['show']]);
    }
  
  /**
     * Display a listing of the resource.
     */
    public function index()
{
    $employees = Employee::all(); 
    return view('admin.pages.payslip.index', compact('employees'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
//     public function generate(Request $request)
// {
//     $employeeId = $request->input('employee');
//     $month = $request->input('month');
//     $year = date('Y'); // Current year

//     // Fetch the payroll record for the selected employee
//     $payroll = PayRoll::where('employee_id', $employeeId)->first();

//     if (!$payroll) {
//         return abort(404, 'Payroll record not found.');
//     }

//     $employee = $payroll->employee;

//     // Fetch attendances for this employee for the selected month
//     $attendances = Attendance::where('employee_id', $employee->id)
//         ->whereMonth('clock_in_date', $month)
//         ->whereYear('clock_in_date', $year)
//         ->get();

//     $absentDaysCount = 0;
//     $startDate = Carbon::createFromDate($year, $month, 1);
//     $currentDate = Carbon::now(); // Current date
//     $totalWorkingDays = 0;
//     $totalWorkMinutes = 0;

//     while ($startDate->month == $month && $startDate <= $currentDate) {
//         $dayOfWeek = $startDate->format('l');
//         if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
//             $totalWorkingDays++;
//             $found = $attendances->contains(function ($attendance) use ($startDate) {
//                 return $attendance->clock_in_date == $startDate->toDateString();
//             });
//             if (!$found) {
//                 $absentDaysCount++;
//             }
//         }
//         $startDate->addDay();
//     }

//     $totalDaysInMonth = $currentDate->daysInMonth;
//     $workingDaysInMonth = 0;

//     for ($day = 1; $day <= $totalDaysInMonth; $day++) {
//         $date = Carbon::createFromDate($year, $month, $day);
//         $dayOfWeek = $date->format('l');
//         if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
//             $workingDaysInMonth++;
//         }
//     }

//     $perDayPay = $workingDaysInMonth > 0 ? $payroll->total / $workingDaysInMonth : 0;
//     $perHour = $perDayPay / 8;

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

//     $paidLeavesCount = $paidLeaves->sum('leave_days');

//     foreach ($attendances as $attendance) {
//         if ($attendance->clock_in_time && $attendance->clock_out_time) {
//             $clockIn = Carbon::parse($attendance->clock_in_time);
//             $clockOut = Carbon::parse($attendance->clock_out_time);
//             $totalWorkMinutes += $clockOut->diffInMinutes($clockIn);
//         }
//     }

//     $totalActualWorkHours = intdiv($totalWorkMinutes, 60);
//     $totalActualWorkMinutes = $totalWorkMinutes % 60;

//     $attendanceDaysCount = $attendances->count();
//     $totalExpectedWorkMinutes = $attendanceDaysCount * 8 * 60;

//     $deductionTotalMinutes = max(0, $totalExpectedWorkMinutes - $totalWorkMinutes);
//     $fullDeductionHours = intdiv($deductionTotalMinutes, 60);
//     $fullDeductionMinutes = $deductionTotalMinutes % 60;
//     $fullDeduction = [
//         'hours' => $fullDeductionHours,
//         'minutes' => $fullDeductionMinutes,
//     ];

//     $deductionHourPay = ($fullDeductionHours + $fullDeductionMinutes / 60) * $perHour;

//     $totalLateMinutes = Attendance::where('employee_id', $employee->id)
//         ->where('clock_in_time', '>', Carbon::createFromTime(10, 15))
//         ->whereMonth('clock_in_date', $month)
//         ->whereYear('clock_in_date', $year)
//         ->get()
//         ->sum(function ($record) {
//             $clockInTime = Carbon::parse($record->clock_in_time);
//             $referenceTime = Carbon::createFromTime(10, 15);
//             return $clockInTime->diffInMinutes($referenceTime);
//         });

//     $deductionTotalMinutes = max(0, $deductionTotalMinutes - $totalLateMinutes);

//     $deductionHours = intdiv($deductionTotalMinutes, 60);
//     $deductionMinutes = $deductionTotalMinutes % 60;

//     $overtimeMinutes = max(0, $totalWorkMinutes - $totalExpectedWorkMinutes);
//     $overtimeHours = intdiv($overtimeMinutes, 60);
//     $overtimeRemainingMinutes = $overtimeMinutes % 60;
//     $overTimePay = $overtimeMinutes * ($perHour / 60);

//     // Calculate total hours worked in the month
//     $totalHours = $totalActualWorkHours + ($totalActualWorkMinutes / 60);

//     return view('admin.pages.payslip.show', compact(
//         'payroll',
//         'absentDaysCount',
//         'perDayPay',
//         'paidLeavesCount',
//         'totalWorkingDays',
//         'totalActualWorkHours',
//         'totalActualWorkMinutes',
//         'totalHours', // Add the total hours to view data
//         'attendances',
//         'attendanceDaysCount',
//         'deductionHours',
//         'deductionMinutes',
//         'deductionHourPay',
//         'perHour',
//         'totalLateMinutes',
//         'overtimeHours',
//         'overtimeRemainingMinutes',
//         'overtimeMinutes',
//         'overTimePay',
//         'fullDeduction',
//         'month', // Ensure you pass the month variable
//         'year' // Ensure you pass the year variable
//     ));
// }

public function generate(Request $request)
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

        // Extract the month from the created_at field
        $uploadMonth = $payslipUpload->created_at->format('n'); // Numeric month (1-12)

        // Check if the month matches the selected month
        if ($uploadMonth == $request->month) {
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

    // Check if a search was performed and no PDFs matched
    if ($request->has('employee') && $request->has('month') && empty($matchingPdfs)) {
        $notFoundMessage = "No payslips found for the selected month.";
    }

    // Pass data to the view
    return view('admin.pages.payslip.index', [
        'employees' => Employee::all(),  // Pass the employee list for the dropdown
        'matchingPdfs' => $matchingPdfs, // Pass matching PDFs for display
        'notFoundMessage' => $notFoundMessage ?? null, // Pass not found message if applicable
    ]);
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
        //
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
        'employee' => $employee,  // Add this line
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
    $pdf = $this->pdf->loadView('admin.pages.payslip.download', $data);

    return $pdf->download('payroll_' . $employee->first_name . '_' . $employee->last_name . '.pdf');
}


    
   
    


    
    
    

    


}
