@extends('admin.master.main')

@section('content')

<div class="container mt-5">
        <div class="card shadow-lg border-light">
            <div class="card-header bg-success text-white rounded-top d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Payroll Details for {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</h2>
                <div class="btn-group">
                    <a href="{{ route('payslip.index') }}" class="btn btn-primary">Back to PaySlip</a>
                    <a href="{{ route('payslip.download', $payroll->id) }}" class="btn btn-primary">Download PaySlip</a>

                    
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card p-3 border-primary">
                            <h5 class="text-primary">Employee Name:</h5>
                            <p class="form-control-plaintext">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3 border-primary">
                            <h5 class="text-primary">Salary:</h5>
                            <p class="form-control-plaintext">{{ number_format($payroll->salary, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card p-3 border-success">
                            <h5 class="text-success">Bonus:</h5>
                            <p class="form-control-plaintext">{{ number_format($payroll->bonus, 2) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3 border-danger">
                            <h5 class="text-danger">Deduction:</h5>
                            <p class="form-control-plaintext">{{ number_format($payroll->deduction, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card p-3 border-info">
                            <h5 class="text-info">Total:</h5>
                            <p class="form-control-plaintext">{{ number_format($payroll->total, 2) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3 border-warning">
                            <h5 class="text-warning">Paid Leaves:</h5>
                            <p class="form-control-plaintext">{{ $paidLeavesCount }}</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card p-3 border-dark">
                            <h5 class="text-dark">Total Absent Days:</h5>
                            <p class="form-control-plaintext">{{ number_format($absentDaysCount) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3 border-dark">
                            <h5 class="text-dark">Absent Days without Paid Leaves:</h5>
                            <p class="form-control-plaintext">{{ number_format($absentDaysCount - $paidLeavesCount) }}</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card p-3 border-secondary">
                            <h5 class="text-secondary">Per Day Pay:</h5>
                            <p class="form-control-plaintext">{{ number_format($perDayPay, 2) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3 border-secondary">
                            <h5 class="text-secondary">Per Hour Pay:</h5>
                            <p class="form-control-plaintext">{{ number_format($perHour, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card p-3 border-success">
                            <h5 class="text-success">Total Working Hours in the Month:</h5>
                            <p class="form-control-plaintext">{{ $totalActualWorkHours }}:{{ str_pad($totalActualWorkMinutes, 2, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3 border-success">
                            <h5 class="text-success">Total Work Hours:</h5>
                            <p class="form-control-plaintext">{{ $attendanceDaysCount*8 }}</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card p-3 border-danger">
                            <h5 class="text-danger">Deduction Work Hours:</h5>
                            <p class="form-control-plaintext">
                                {{ max(0, $fullDeduction['hours']) }}:{{ str_pad(max(0, $fullDeduction['minutes']), 2, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3 border-danger">
                            <h5 class="text-danger">Deduction Hour Pay:</h5>
                            <p class="form-control-plaintext">{{ number_format($deductionHourPay, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card p-3 border-warning">
                            <h5 class="text-warning">Absent Days Deduction:</h5>
                            <p class="form-control-plaintext">{{ number_format(($absentDaysCount - $paidLeavesCount) * $perDayPay, 2) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3 border-warning">
                            <h5 class="text-warning">OverTime:</h5>
                            <p class="form-control-plaintext">{{ $overtimeHours }}:{{ str_pad($overtimeRemainingMinutes, 2, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card p-3 border-info">
                            <h5 class="text-info">OverTime Pay:</h5>
                            <p class="form-control-plaintext">{{ number_format($overTimePay, 2) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card p-3 border-info">
                            <h5 class="text-info">Net Salary:</h5>
                            <p class="form-control-plaintext">
                                {{ number_format(($payroll->total - (($absentDaysCount - $paidLeavesCount) * $perDayPay) - $deductionHourPay) + $overTimePay, 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $currentMonth = \Carbon\Carbon::now()->month;
            $currentYear = \Carbon\Carbon::now()->year;
            $payrollMonth = $month; // assuming $month is the payroll month
            $payrollYear = $year;   // assuming $year is the payroll year
        @endphp

        @if ($currentMonth == $payrollMonth && $currentYear == $payrollYear)
            <h3 class="mt-4">Attendance Details for {{ \Carbon\Carbon::create()->month($month)->format('F') }} {{ $year }}</h3>
            <table class="table table-striped table-bordered mt-3">
                <thead class="thead-dark">
                    <tr>
                        <th>Date</th>
                        <th>Clock In Time</th>
                        <th>Clock Out Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->clock_in_date }}</td>
                            <td>{{ $attendance->clock_in_time }}</td>
                            <td>{{ $attendance->clock_out_time }}</td>
                            <td>{{ $attendance->reason }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

@endsection
