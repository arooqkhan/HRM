<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Slip</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            padding: 20px;
        }
        .item {
            margin-bottom: 10px;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        img {
            height: 100px;
            width: 100px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
<div class="container">
<div class="text-center mb-4">
        <img src="https://hrmfiles.com/public/images/naxas.png" alt="Company Logo" class="logo">
        </div>
    <div class="header">
        <h1>PaySlip</h1>
        <p><strong>Employee:</strong> {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</p>
    </div>

    <div class="content">
   
        <div class="row">
            <div class="col-md-6">
                <div class="item">
                    <strong>Salary:</strong> {{ number_format($payroll->salary, 2) }}
                </div>
                <div class="item">
                    <strong>Bonus:</strong> {{ number_format($payroll->bonus, 2) }}
                </div>
                <div class="item">
                    <strong>Deduction:</strong> {{ number_format($payroll->deduction, 2) }}
                </div>
                <div class="item">
                    <strong>Paid Leaves Count:</strong> {{ $paidLeavesCount }}
                </div>
                <div class="item">
                    <strong>Absent Days Count:</strong> {{ number_format($absentDaysCount) }}
                </div>
                <div class="item">
                    <strong>Absent Days without Paid Leaves:</strong> {{ number_format($absentDaysCount - $paidLeavesCount) }}
                </div>
                <div class="item">
                    <strong>Per Day Pay:</strong> {{ number_format($perDayPay, 2) }}
                </div>
                <div class="item">
                    <strong>Per Hour Pay:</strong> {{ number_format($perHour, 2) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="item">
                    <strong>Total Working Hours in the Month:</strong> {{ $totalActualWorkHours }} Hours {{ $totalActualWorkMinutes }} Minutes
                </div>
                <div class="item">
                    <strong>Total Work Hours:</strong> {{ $attendanceDaysCount * 8 }}
                </div>
                <div class="item">
                    <strong>Deduction Work Hours:</strong> {{ max(0, $fullDeduction['hours']) }} Hours {{ str_pad(max(0, $fullDeduction['minutes']), 2, '0', STR_PAD_LEFT) }} Minutes
                </div>
                <div class="item">
                    <strong>Deduction Hour Pay:</strong> {{ number_format($deductionHourPay, 2) }}
                </div>
                <div class="item">
                    <strong>Absent Days Deduction:</strong> {{ number_format(($absentDaysCount - $paidLeavesCount) * $perDayPay, 2) }}
                </div>
                <div class="item">
                    <strong>Overtime Hours:</strong> {{ $overtimeHours }} Hours {{ str_pad($overtimeRemainingMinutes, 2, '0', STR_PAD_LEFT) }} Minutes
                </div>
                <div class="item">
                    <strong>Overtime Pay:</strong> {{ number_format($overTimePay, 2) }}
                </div>
                <div class="item">
                    <strong>Net Salary:</strong> {{ number_format(($payroll->total - (($absentDaysCount - $paidLeavesCount) * $perDayPay) - $deductionHourPay) + $overTimePay, 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="footer text-center mt-4">
        <p><strong>Naxas</strong> - 44-A Civic Center Bahria Town Phase 4</p>
        <p>Contact: (123) 456-7890 | Email: info@company.com</p>
    </div>
</div>

<!-- Bootstrap JS and dependencies (optional) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
