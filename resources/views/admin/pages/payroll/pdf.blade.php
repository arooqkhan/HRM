<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <title>Payroll Details</title>
    <style>
        .logo {
            max-width: 100px;
        }
        .payroll-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }
        .payroll-section h5 {
            color: #0d6efd;
            font-size: 1rem;
            margin-bottom: 5px;
        }
        .signature-section {
            margin-top: 30px;
        }
        .signature-section p {
            font-weight: 500;
        }
        .footer {
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Header with Logo -->
        <div class="text-center mb-4">
            <img src="https://hrmfiles.com/public/images/naxas.png" alt="Company Logo" class="logo">
            <h1 class="my-3">Payroll Details</h1>
        </div>
        
        <!-- Payroll Details Section -->
        <div class="payroll-section">
        <div class="container">
        <h2 class="text-center">Payroll Details {{$payroll->employee->first_name}} {{ $payroll->employee->last_name }}</h2>
        <div class="row">
            <!-- Existing rows for payroll details -->
            <div class="col-md-4">
                <h5>Employee Name:</h5>
                <p>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</p>
            </div>
            <div class="col-md-4">
                <h5>Salary:</h5>
                <p>{{ number_format($payroll->salary, 2) }}</p>
            </div>
            <div class="col-md-4">
                <h5>Bonus:</h5>
                <p>{{ number_format($payroll->bonus, 2) }}</p>
            </div>
            <div class="col-md-4">
                <h5>Deduction:</h5>
                <p>{{ number_format($payroll->deduction, 2) }}</p>
            </div>
            <div class="col-md-4">
                <h5>Total:</h5>
                <p>{{ number_format($payroll->total, 2) }}</p>
            </div>
            <div class="col-md-4">
                <h5>Paid Leaves:</h5>
                <p>{{ $paidLeavesCount }}</p>
            </div>
            <div class="col-md-4">
                <h5>Total Absent Days:</h5>
                <p>{{ number_format($absentDaysCount) }}</p>
            </div>
            <div class="col-md-4">
                <h5>Absent Days without Paid Leaves:</h5>
                <p>{{ number_format($absentDaysCount - $paidLeavesCount) }}</p>
            </div>
            <div class="col-md-4">
                <h5>Per Day Pay:</h5>
                <p>{{ number_format($payroll->total/$absentDaysCount, 2) }}</p>
            </div>
            
            <div class="col-md-4">
                <h5>Total Working Hours in the Month:</h5>
                <p>{{ $absentDaysCount*8 }}</p>
            </div>
            <div class="col-md-4">
                <h5>Total Actual Work Hours:</h5>
                <p>{{ $formattedTotalActualWorkHours }}</p>
            </div>
           

<div class="col-md-4">
    <h5>Deduction Hour Pay:</h5>
    <p>{{ number_format($deductionHourPay, 2) }}</p>
</div>
            <div class="col-md-4">
                <h5>Absent Days Deduction:</h5>
                <p> {{ number_format(min($payroll->total, ($absentDaysCount - $paidLeavesCount) * $perDayPay), 2) }}</p>
            </div>
            <div class="col-md-4">
                <h5>OverTime:</h5>
                <p>{{ $overtimeHours }}:{{ str_pad($overtimeRemainingMinutes, 2, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="col-md-4">
                <h5>OverTime Pay:</h5>
                <p>{{ number_format($overTimePay, 2) }}</p>
            </div>
            <div class="col-md-4">
    <h5>Net Salary:</h5>
    {{ number_format(max(0, ($payroll->total - (($absentDaysCount - $paidLeavesCount) * $perDayPay) - $deductionHourPay) + $overTimePay), 2) }}
</div>
        </div>
    </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section row mt-4">
            <div class="col-md-6">
                <p><strong>Employee Signature:</strong> ____________________</p>
            </div>
            <div class="col-md-6 text-end">
                <p><strong>HR Signature:</strong> ____________________</p>
            </div>
        </div>
        
        <!-- Footer with Company Details -->
        <div class="footer text-center mt-4">
            <p><strong>Naxas</strong> - 44-A Civic Center Bahria Town Phase 4</p>
            <p>Contact: (123) 456-7890 | Email: info@company.com</p>
        </div>
    </div>
</body>
</html>
