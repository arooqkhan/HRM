<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\PayRoll;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiPayRollController extends Controller
{
    public function showForCurrentEmployee(Request $request)
    {
        try {
            // Retrieve the authenticated user's role and employee ID
            $user = $request->user();
            $employeeId = $user->employee_id;
            $isAdmin = $user->role === 'admin'; // Adjust this according to your role setup

            if (!$employeeId && !$isAdmin) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            if ($isAdmin) {
                // Admin can see all records
                $payrolls = PayRoll::with('employee')->get();
                $response = $payrolls->map(function ($payroll) {
                    if (!$payroll->employee) {
                        // If no employee is associated with this payroll, skip this record or handle it accordingly
                        return [
                            'id' => null,
                            'name' => 'No Employee Found',
                            'salary' => number_format($payroll->total, 2),
                            'bonus' => number_format($payroll->bonus, 2),
                            'total_deduction' => 'N/A',
                            'net_salary' => 'N/A'
                        ];
                    }

                    $employeeId = $payroll->employee->id;

                    // Calculate payroll details
                    $attendances = Attendance::where('employee_id', $employeeId)
                        ->whereMonth('clock_in_date', Carbon::now()->month)
                        ->whereYear('clock_in_date', Carbon::now()->year)
                        ->get();

                    $absentDaysCount = 0;
                    $startDate = Carbon::now()->startOfMonth();
                    $currentDate = Carbon::now();
                    $totalWorkingDays = 0;

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

                    $totalDaysInMonth = $currentDate->daysInMonth;
                    $workingDaysInMonth = 0;

                    for ($day = 1; $day <= $totalDaysInMonth; $day++) {
                        $date = Carbon::createFromDate($currentDate->year, $currentDate->month, $day);
                        $dayOfWeek = $date->format('l');
                        if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
                            $workingDaysInMonth++;
                        }
                    }

                    if ($workingDaysInMonth == 0) {
                        $total_deduction = 0;
                        $net_salary = 0;
                    } else {
                        $perDayPay = $payroll->total / $workingDaysInMonth;

                        $paidLeaves = Leave::where('employee_id', $employeeId)
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

                        $paidLeavesCount = $paidLeaves->sum('leave_days');
                        $totalDeductionForAbsentDays = ($absentDaysCount - $paidLeavesCount) * $perDayPay;

                        $totalMinutesLate = 0;
                        $attendanceRecords = Attendance::where('employee_id', $employeeId)
                            ->where('clock_in_time', '>', Carbon::createFromTime(10, 15))
                            ->whereMonth('clock_in_date', Carbon::now()->month)
                            ->whereYear('clock_in_date', Carbon::now()->year)
                            ->get();

                        foreach ($attendanceRecords as $record) {
                            $clockInTime = Carbon::parse($record->clock_in_time);
                            $referenceTime = Carbon::createFromTime(10, 15);
                            $differenceInMinutes = $clockInTime->diffInMinutes($referenceTime);
                            $totalMinutesLate += $differenceInMinutes;
                        }

                        $totalLateHours = intdiv($totalMinutesLate, 60);
                        $totalLateMinutes = $totalMinutesLate % 60;

                        $deductionForLateHours = $totalLateHours * $perDayPay / 8;
                        $deductionForLateMinutes = ($totalLateMinutes / 60) * ($perDayPay / 8);

                        $totalDeductionForLateHours = $deductionForLateHours + $deductionForLateMinutes;

                        $total_deduction = $totalDeductionForAbsentDays + $totalDeductionForLateHours;
                        $net_salary = $payroll->total - $total_deduction;
                    }

                    return [
                        'id' => $employeeId,
                        'name' => $payroll->employee->first_name . ' ' . $payroll->employee->last_name,
                        'salary' => number_format($payroll->total, 2),
                        'bonus' => number_format($payroll->bonus, 2),
                        'total_deduction' => number_format($total_deduction, 2),
                        'net_salary' => number_format($net_salary, 2)
                    ];
                });

                return response()->json($response->toArray());
            }
            // Convert the collection to an array
            else {
                // Non-admin user sees only their own record
                $payroll = PayRoll::where('employee_id', $employeeId)->first();

                if (!$payroll) {
                    return response()->json(['error' => 'Payroll record not found for this employee.'], 404);
                }

                if (!is_numeric($payroll->total)) {
                    return response()->json(['error' => 'Invalid payroll total value: ' . $payroll->total], 400);
                }

                $attendances = Attendance::where('employee_id', $employeeId)
                    ->whereMonth('clock_in_date', Carbon::now()->month)
                    ->whereYear('clock_in_date', Carbon::now()->year)
                    ->get();

                $absentDaysCount = 0;
                $startDate = Carbon::now()->startOfMonth();
                $currentDate = Carbon::now();
                $totalWorkingDays = 0;

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

                $totalDaysInMonth = $currentDate->daysInMonth;
                $workingDaysInMonth = 0;

                for ($day = 1; $day <= $totalDaysInMonth; $day++) {
                    $date = Carbon::createFromDate($currentDate->year, $currentDate->month, $day);
                    $dayOfWeek = $date->format('l');
                    if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
                        $workingDaysInMonth++;
                    }
                }

                if ($workingDaysInMonth == 0) {
                    $total_deduction = 0;
                    $net_salary = 0;
                } else {
                    $perDayPay = $payroll->total / $workingDaysInMonth;

                    $paidLeaves = Leave::where('employee_id', $employeeId)
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

                    $paidLeavesCount = $paidLeaves->sum('leave_days');
                    $totalDeductionForAbsentDays = ($absentDaysCount - $paidLeavesCount) * $perDayPay;

                    $totalMinutesLate = 0;
                    $attendanceRecords = Attendance::where('employee_id', $employeeId)
                        ->where('clock_in_time', '>', Carbon::createFromTime(10, 15))
                        ->whereMonth('clock_in_date', Carbon::now()->month)
                        ->whereYear('clock_in_date', Carbon::now()->year)
                        ->get();

                    foreach ($attendanceRecords as $record) {
                        $clockInTime = Carbon::parse($record->clock_in_time);
                        $referenceTime = Carbon::createFromTime(10, 15);
                        $differenceInMinutes = $clockInTime->diffInMinutes($referenceTime);
                        $totalMinutesLate += $differenceInMinutes;
                    }

                    $totalLateHours = intdiv($totalMinutesLate, 60);
                    $totalLateMinutes = $totalMinutesLate % 60;

                    $deductionForLateHours = $totalLateHours * $perDayPay / 8;
                    $deductionForLateMinutes = ($totalLateMinutes / 60) * ($perDayPay / 8);

                    $totalDeductionForLateHours = $deductionForLateHours + $deductionForLateMinutes;

                    $total_deduction = $totalDeductionForAbsentDays + $totalDeductionForLateHours;
                    $net_salary = $payroll->total - $total_deduction;
                }

                return response()->json([
                    [
                        'id' => $employeeId,
                        'name' => $payroll->employee->first_name . ' ' . $payroll->employee->last_name,
                        'salary' => number_format($payroll->total, 2),
                        'bonus' => number_format($payroll->bonus, 2),
                        'total_deduction' => number_format($total_deduction, 2),
                        'net_salary' => number_format($net_salary, 2)
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }




    public function showForCurrentEmployeeSlip(Request $request)
    {
        try {
            // Retrieve the authenticated user's role and employee ID
            $user = $request->user();
            $employeeId = $user->employee_id;
            $isAdmin = $user->role === 'admin'; // Adjust this according to your role setup

            if (!$employeeId && !$isAdmin) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $response = [];

            if ($isAdmin) {
                // Admin can see all records
                $payrolls = PayRoll::with('employee')->get();
                $response = $payrolls->map(function ($payroll) {
                    return $this->calculatePayrollDetails($payroll);
                })->toArray();
            } else {
                // Non-admin user sees only their own record
                $payroll = PayRoll::where('employee_id', $employeeId)->first();

                if (!$payroll) {
                    return response()->json(['error' => 'Payroll record not found for this employee.'], 404);
                }

                if (!is_numeric($payroll->total)) {
                    return response()->json(['error' => 'Invalid payroll total value: ' . $payroll->total], 400);
                }

                $response[] = $this->calculatePayrollDetails($payroll);
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    private function calculatePayrollDetails($payroll)
    {
        $employeeId = $payroll->employee->id;

        // Calculate payroll details
        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereMonth('clock_in_date', Carbon::now()->month)
            ->whereYear('clock_in_date', Carbon::now()->year)
            ->get();

        $absentDaysCount = 0;
        $startDate = Carbon::now()->startOfMonth();
        $currentDate = Carbon::now();
        $totalWorkingDays = 0;

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

        $totalDaysInMonth = $currentDate->daysInMonth;
        $workingDaysInMonth = 0;

        for ($day = 1; $day <= $totalDaysInMonth; $day++) {
            $date = Carbon::createFromDate($currentDate->year, $currentDate->month, $day);
            $dayOfWeek = $date->format('l');
            if (!in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
                $workingDaysInMonth++;
            }
        }

        if ($workingDaysInMonth == 0) {
            $total_deduction = 0;
            $net_salary = 0;
        } else {
            $perDayPay = $payroll->total / $workingDaysInMonth;

            $paidLeaves = Leave::where('employee_id', $employeeId)
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

            $paidLeavesCount = $paidLeaves->sum('leave_days');
            $totalDeductionForAbsentDays = ($absentDaysCount - $paidLeavesCount) * $perDayPay;

            $totalMinutesLate = 0;
            $attendanceRecords = Attendance::where('employee_id', $employeeId)
                ->where('clock_in_time', '>', Carbon::createFromTime(10, 15))
                ->whereMonth('clock_in_date', Carbon::now()->month)
                ->whereYear('clock_in_date', Carbon::now()->year)
                ->get();

            foreach ($attendanceRecords as $record) {
                $clockInTime = Carbon::parse($record->clock_in_time);
                $referenceTime = Carbon::createFromTime(10, 15);
                $differenceInMinutes = $clockInTime->diffInMinutes($referenceTime);
                $totalMinutesLate += $differenceInMinutes;
            }

            $totalLateHours = intdiv($totalMinutesLate, 60);
            $totalLateMinutes = $totalMinutesLate % 60;

            $deductionForLateHours = $totalLateHours * $perDayPay / 8;
            $deductionForLateMinutes = ($totalLateMinutes / 60) * ($perDayPay / 8);

            $totalDeductionForLateHours = $deductionForLateHours + $deductionForLateMinutes;

            $total_deduction = $totalDeductionForAbsentDays + $totalDeductionForLateHours;
            $net_salary = $payroll->total - $total_deduction;
        }

        return [
            'id' => $employeeId,
            'name' => $payroll->employee->first_name . ' ' . $payroll->employee->last_name,
            'salary' => number_format($payroll->total, 2),
            'bonus' => number_format($payroll->bonus, 2),
            'per_day_pay' => number_format($perDayPay, 2),
            'per_hour_pay' => number_format($perDayPay / 8, 2),
            'total_absent_days' => $absentDaysCount,
            'paid_leaves' => $paidLeavesCount,
            'absent_days_deduction' => number_format($totalDeductionForAbsentDays, 2),
            'Late_hours_deduction' => number_format($totalDeductionForLateHours, 2),
            'total_deduction' => number_format($total_deduction, 2),
            'net_salary' => number_format($net_salary, 2),
        ];
    }


    public function showForEmployeeById(Request $request, $employeeId)
    {
        try {
            $user = $request->user();
            $isAdmin = $user->role === 'admin';

            // Check if the user is an admin or if the employee ID matches the authenticated user's ID
            if (!$isAdmin && $user->employee_id !== $employeeId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $payroll = PayRoll::with('employee')->where('employee_id', $employeeId)->first();

            if (!$payroll) {
                return response()->json(['error' => 'Payroll record not found for this employee.'], 404);
            }

            if (!is_numeric($payroll->total)) {
                return response()->json(['error' => 'Invalid payroll total value: ' . $payroll->total], 400);
            }

            $response = $this->calculatePayrollDetails($payroll);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
