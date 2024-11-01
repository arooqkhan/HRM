<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Shift</title>
</head>
<body>
   <p>You have received an assigned shift on date <strong>{{ $shift->date }}</strong> from <strong>{{ date('h:i A', strtotime($shift->start_time)) }}</strong> to <strong>{{ date('h:i A', strtotime($shift->end_time)) }}</strong></p>
   <p>Thank you,</p>
   <p>The Company Team</p>
</body>
</html>
