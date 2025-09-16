<!DOCTYPE html>
<html>
<head>
    <title>Expenses - {{ ucfirst($period) }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .signature {
            margin-top: 20px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 14px;
            color: #555;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 150px;
        }
    </style>
</head>
<body>

    <div class="logo-container">
        <img src="https://hrmfiles.com/public/images/naxas.png" alt="Company Logo" class="logo">
    </div>
    
    <h1>Expenses for {{ ucfirst($period) }}</h1>
    
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Date</th>
            </tr>   
        </thead>
        <tbody>
            @foreach($expenses as $expenseItem)
                <tr>
                    <td>{{ $expenseItem->name }}</td>
                    <td>{{ number_format($expenseItem->price, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($expenseItem->date)->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align: right;"><strong>Total Price:</strong></td>
                <td><strong>{{ number_format($expenses->sum('price'), 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="signature">
        <p><strong>HR Signature:</strong> ______________</p>
    </div>

    <div class="footer">
        <p><strong>Naxas</strong> - 44-A Civic Center Bahria Phase 4 - Contact: (123) 456-7890</p>
        <p>Email: info@company.com</p>
    </div>
</body>
</html>
