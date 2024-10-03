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
        .header img {
            max-width: 120px;
        }
        .details {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .details .border {
            border-color: #e0e0e0;
        }
        .signature-section p {
            margin-bottom: 0;
        }
        .footer {
            border-top: 1px solid #ddd;
            padding-top: 10px;
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
        
        <!-- Payroll Details -->
        <div class="details p-4 shadow-sm">
            <h3 class="mb-4">Employee: {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</h3>
            
            <!-- Payroll Records in Grid -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="p-3 border bg-white rounded">
                        <p><strong>Bonus:</strong> {{ number_format($payroll->bonus, 2) }}</p>
                        <p><strong>Salary:</strong> {{ number_format($payroll->total, 2) }}</p>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="p-3 border bg-white rounded">
                        <p><strong>Total Deduction:</strong> {{ number_format((($absentDaysCount - $paidLeavesCount) * $perDayPay) + $deductionHourPay, 2) }}</p>
                        <p><strong>OverTime Pay:</strong> {{ number_format($overTimePay, 2) }}</p>
                        <p><strong>Net Salary:</strong> {{ number_format(($payroll->total - (($absentDaysCount - $paidLeavesCount) * $perDayPay) - $deductionHourPay) + $overTimePay, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="signature-section row mt-4">
            <div class="col-md-6">
                <p><strong>Employee Signature:</strong> ____________________</p>
            </div>
<br><br>
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
