<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Shift Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif; /* Use a readable font */
            margin: 20px; /* Add some margin */
            padding: 20px; /* Add padding */
            background-color: #f9f9f9; /* Light background for better contrast */
            border: 1px solid #ddd; /* Light border */
            border-radius: 5px; /* Rounded corners */
        }
        p {
            line-height: 1.5; /* Improved line spacing */
        }
        .employee-name {
            font-weight: bold; /* Make the employee name bold */
        }
    </style>
</head>
<body>
   <p class="employee-name">{{$shift->employee->first_name}} {{$shift->employee->last_name}} has Accepted their shift.</p>
   <p>Thank you,</p>
   <p>The Company Team</p>
</body>
</html>