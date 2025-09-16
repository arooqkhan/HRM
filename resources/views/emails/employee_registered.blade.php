<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Registered</title>
</head>
<body>
    <h1>Welcome, {{ $employee->first_name }}!</h1> <!-- Accessing first_name directly -->
    <p>Dear {{ $employee->first_name }} {{ $employee->last_name }},</p>
    <p>Congratulations! You have been enrolled as an employee ({{$employee->role}}) at Naxas Limited. 
        Please use this link <a href="https://hrmfiles.com/">Sign in to the system</a> in to the system using your email <strong>{{$email}}</strong> and password <strong>{{$password}}</strong>.
    </p>
    
    <p>Thank you,</p>
    <p>The Company Team</p>
</body>
</html>
