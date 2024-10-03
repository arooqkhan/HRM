@extends('admin.master.main')
@section('content')
<div class="container mt-5">
    <div class="card shadow-sm border-light">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white rounded-top">
            <h2 class="mb-0">Payroll Details for {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</h2>
            <div>
                <a href="{{ route('payroll.index') }}" class="btn btn-success">Back to Payroll List</a>
                <a href="{{ route('payroll.download', $payroll->id) }}" class="btn btn-success">Download Payroll</a>
            </div>
        </div>
        <div class="card-body">

            <div class="row">
                <!-- First Column -->
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Employee Name:</h5>
                    <p class="form-control-plaintext">{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Salary:</h5>
                    <p class="form-control-plaintext">{{ number_format($payroll->salary, 2) }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Bonus:</h5>
                    <p class="form-control-plaintext">{{ number_format($payroll->bonus, 2) }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Deduction:</h5>
                    <p class="form-control-plaintext">{{ number_format($payroll->deduction, 2) }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Total:</h5>
                    <p class="form-control-plaintext">{{ number_format($payroll->total, 2) }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Paid Leaves:</h5>
                    <p class="form-control-plaintext">{{ $paidLeavesCount }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Total Absent Days:</h5>
                    <p class="form-control-plaintext">{{ number_format($absentDaysCount) }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Absent Days without Paid Leaves:</h5>
                    <p class="form-control-plaintext">{{ number_format($absentDaysCount - $paidLeavesCount) }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Per Day Pay:</h5>
                    <p class="form-control-plaintext">{{ number_format($perDayPay, 2) }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Per Hour Pay:</h5>
                    <p class="form-control-plaintext">{{ number_format($perHour, 2) }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Total Working Hours in the Month:</h5>
                    <p class="form-control-plaintext">{{ $totalHours }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Total Actual Work Hours:</h5>
                    <p class="form-control-plaintext">{{ $totalActualWorkHours }}:{{ $totalActualWorkMinutes }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Total Work Hours:</h5>
                    <p class="form-control-plaintext">{{ $attendanceDaysCount }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Deduction Work Hours:</h5>
                    <p class="form-control-plaintext">
                        {{ max(0, $fullDeduction['hours']) }}:{{ str_pad(max(0, $fullDeduction['minutes']), 2, '0', STR_PAD_LEFT) }}
                    </p>
                </div>

                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Deduction Hour Pay:</h5>
                    <p class="form-control-plaintext">{{ number_format($deductionHourPay, 2) }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Absent Days Deduction:</h5>
                    <p class="form-control-plaintext">{{ number_format(($absentDaysCount - $paidLeavesCount) * $perDayPay, 2) }}</p>
                </div>

                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">OverTime:</h5>
                    <p class="form-control-plaintext">{{ $overtimeHours }}:{{ str_pad($overtimeRemainingMinutes, 2, '0', STR_PAD_LEFT) }}</p>
                </div>

                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">OverTime Pay:</h5>
                    <p class="form-control-plaintext">{{ number_format($overTimePay,2) }}</p>
                </div>


                <div class="col-md-4 mb-3">
                    <h5 class="text-primary">Net Salary:</h5>
                    <p class="form-control-plaintext">
                        {{ number_format(($payroll->total - (($absentDaysCount - $paidLeavesCount) * $perDayPay) - $deductionHourPay) + $overTimePay, 2) }}
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection