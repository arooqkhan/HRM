<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ApiAttendanceController extends Controller
{
    
    public function checkIn(Request $request)
    {
     
        $user = Auth::user();
        $employee_id = $user->employee_id; // Get employee_id from the authenticated user
        
        // Ensure employee_id is valid
        if (!$employee_id) {
            return response()->json([
                'message' => 'Employee ID not found'
            ], 404);
        }
        
        // Retrieve the office location from environment or config
        $officeLatitude = env('OFFICE_LATITUDE', 0.0);
        $officeLongitude = env('OFFICE_LONGITUDE', 0.0);
        $maxDistance = env('MAX_CHECKIN_DISTANCE', 50); // Maximum distance in meters
        
        // Get user's current location from the request
        $userLatitude = $request->input('latitude');
        $userLongitude = $request->input('longitude');
        
        if ($userLatitude === null || $userLongitude === null) {
            return response()->json([
                'message' => 'Location information is required'
            ], 400);
        }
        
        // Calculate the distance between the user and the office
        $distance = $this->calculateDistance($officeLatitude, $officeLongitude, $userLatitude, $userLongitude);
        
        if ($distance > $maxDistance) {
            return response()->json([
                'message' => 'You are too far from the office to check in'
            ], 403);
        }
        
        // Get current date and time
        $currentDateTime = Carbon::now();
        $currentDate = $currentDateTime->toDateString();
        $currentTime = $currentDateTime->toTimeString();
        
        // Check if the employee has already checked in for today
        $existingAttendance = Attendance::where('employee_id', $employee_id)
                                        ->where('clock_in_date', $currentDate)
                                        ->first();
        
        if ($existingAttendance) {
            // Calculate time passed since the last check-in
            $checkInTime = Carbon::parse($existingAttendance->clock_in_time);
            $timePassed = $checkInTime->diff($currentDateTime); // Get a DateInterval object
    
            // Format the time passed as HH:MM:SS
            $timePassedFormatted = sprintf(
                '%02d:%02d:%02d',
                $timePassed->h,
                $timePassed->i,
                $timePassed->s
            );
    
            return response()->json([
                'message' => 'You have already checked in today',
                'distance' => $distance,
                'employee' => [
                    'employee_id' => $employee_id,
                    'check_in_time' => $existingAttendance->clock_in_time,
                    'time_passed' => $timePassedFormatted
                ]
            ], 400); // You can adjust the status code as needed
        }
        
        // Create a new attendance record for check-in
        $attendance = new Attendance();
        $attendance->employee_id = $employee_id;
        $attendance->clock_in_date = $currentDate;
        $attendance->clock_in_time = $currentTime;
        $attendance->clock_out_date = null;
        $attendance->clock_out_time = null;
        $attendance->reason = 'Online attendance';
        $attendance->save(); // Save the attendance record to the database
        
        // Return success response
        return response()->json([
            'message' => 'Check-in successful',
            'distance' => $distance,
            'employee' => [
                'employee_id' => $employee_id,
                'check_in_time' => $currentTime,
                'time_passed' => '00:00:00' // No time passed for a new check-in
            ]
        ], 200);
    }
    
    
    
    protected function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius of Earth in meters
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;

        
    }


    // public function checkIn(Request $request)
    // {
    //     $user = Auth::user();
    //     $employee_id = $user->employee_id; // Get employee_id from the authenticated user
    
    //     // Ensure employee_id is valid
    //     if (!$employee_id) {
    //         return response()->json([
    //             'message' => 'Employee ID not found'
    //         ], 404);
    //     }
    
    //     // Get current date and time with the correct time zone
    //     $currentDateTime = Carbon::now('Asia/Karachi'); // Replace with your time zone
    //     $currentDate = $currentDateTime->toDateString();
    //     $currentTime = $currentDateTime->toTimeString();
    
    //     // Log current date and time for debugging purposes
    //     Log::info('Current date and time:', ['date' => $currentDate, 'time' => $currentTime]);
    
    //     // Check if the user has a pending check-out from a previous day
    //     $pendingCheckout = Attendance::where('employee_id', $employee_id)
    //         ->whereNull('clock_out_time') // Check if there's no check-out
    //         ->whereDate('clock_in_date', '<', $currentDate) // Ensure the clock-in date is before today
    //         ->first();

          
    
    //     if ($pendingCheckout) {
    //         // Automatically check out the user for the previous day before allowing today's check-in
    //         $pendingCheckout->clock_out_date = $pendingCheckout->clock_in_date;
    //         $pendingCheckout->clock_out_time = '18:00:00'; // Set check-out time to the end of the previous day
    //         $pendingCheckout->save();
    
    //         // Log the auto checkout for the previous day
    //         Log::info('Automatically checked out for previous day.', ['employee_id' => $employee_id, 'clock_in_date' => $pendingCheckout->clock_in_date]);
    //     } else {
    //         // Log that no pending checkout was found
    //         Log::info('No pending checkout found for previous day.', ['employee_id' => $employee_id]);
    //     }
    
    //     // Check if there's already a check-in record for today
    //     $existingAttendance = Attendance::where('employee_id', $employee_id)
    //         ->where('clock_in_date', $currentDate)
    //         ->whereNotNull('clock_in_time') // Ensure it's not null to identify actual check-ins
    //         ->first();
    
    //     if ($existingAttendance) {
    //         // Calculate the time difference in seconds between check-in time and current time
    //         $checkInTime = Carbon::parse($existingAttendance->clock_in_time);
    //         $timeElapsedInSeconds = $currentDateTime->diffInSeconds($checkInTime);
    
    //         // Convert seconds into hours, minutes, and seconds
    //         $hours = floor($timeElapsedInSeconds / 3600);
    //         $minutes = floor(($timeElapsedInSeconds % 3600) / 60);
    //         $seconds = $timeElapsedInSeconds % 60;
    
    //         // Format the time as HH:MM:SS
    //         $timeElapsedFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    
    //         return response()->json([
    //             'message' => 'You have already checked in today.',
    //             'status' => 400,
    //             'attendance' => [
    //                 'employee_id' => $existingAttendance->employee_id,
    //                 'clock_in_date' => $existingAttendance->clock_in_date,
    //                 'clock_in_time' => $existingAttendance->clock_in_time,
    //                 'clock_out_date' => $existingAttendance->clock_out_date,
    //                 'clock_out_time' => $existingAttendance->clock_out_time,
    //                 'reason' => $existingAttendance->reason,
    //                 'time_pass' => $timeElapsedFormatted, // Return formatted time
    //             ]
    //         ], 400);
    //     }
    
    //     // Create a new attendance record for check-in
    //     $attendance = new Attendance();
    //     $attendance->employee_id = $employee_id;
    //     $attendance->clock_in_date = $currentDate;
    //     $attendance->clock_in_time = $currentTime;
    //     $attendance->clock_out_date = null;
    //     $attendance->clock_out_time = null;
    //     $attendance->reason = 'Online attendance';
    //     $attendance->save();
    
    //     // Log the successful check-in
    //     Log::info('Check-in successful.', ['employee_id' => $employee_id, 'clock_in_date' => $currentDate, 'clock_in_time' => $currentTime]);
    
    //     return response()->json([
    //         'message' => 'Check-in successful',
    //         'status' => 200,
    //         'attendance' => [
    //             'employee_id' => $attendance->employee_id,
    //             'clock_in_date' => $attendance->clock_in_date,
    //             'clock_in_time' => $attendance->clock_in_time,
    //             'clock_out_date' => $attendance->clock_out_date,
    //             'clock_out_time' => $attendance->clock_out_time,
    //             'reason' => $attendance->reason,
    //             'time_pass' => '00:00:00' // Time passed for new check-ins is zero
    //         ]
    //     ], 200);
    // }
    


    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $employee_id = $user->employee_id; 
    
        // Find today's attendance record for the employee
        $attendance = Attendance::where('employee_id', $employee_id)
            ->whereDate('clock_in_date', Carbon::now()->toDateString())
            ->whereNotNull('clock_in_time')
            ->first();
    
        if (!$attendance) {
            return response()->json([
                'message' => 'No check-in record found for today'
            ], 404);
        }
    
        // Check if the employee has already checked out
        if ($attendance->clock_out_time) {
            return response()->json([
                'message' => 'You have already checked out today',
            ], 400);
        }
    
        // Get current time for check-out
        $clockOutTime = Carbon::now();
    
        // Calculate the time difference between check-in and check-out
        $checkInTime = Carbon::parse($attendance->clock_in_time);
        $timeElapsedInSeconds = $clockOutTime->diffInSeconds($checkInTime);
    
        // Convert seconds into hours, minutes, and seconds
        $hours = floor($timeElapsedInSeconds / 3600);
        $minutes = floor(($timeElapsedInSeconds % 3600) / 60);
        $seconds = $timeElapsedInSeconds % 60;
    
        // Format the time as HH:MM:SS
        $timeElapsedFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    
        // Update the attendance record for check-out
        $attendance->clock_out_date = $clockOutTime->toDateString();
        $attendance->clock_out_time = $clockOutTime->toTimeString();
        $attendance->save();
    
        return response()->json([
            'message' => 'Check-out successful',
            'attendance' => $attendance,
            'time_spent' => $timeElapsedFormatted, // Show time spent between check-in and check-out
        ], 200);
    }


    public function getCurrentMonthAttendance(Request $request)
    {
        try {
            // Retrieve the authenticated user
            $user = $request->user();
            $employeeId = $user->employee_id;

            // Get the current month and year
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            // Fetch all attendance records for the current month up to today
            $attendances = Attendance::where('employee_id', $employeeId)
                ->whereMonth('clock_in_date', $currentMonth)
                ->whereYear('clock_in_date', $currentYear)
                ->whereDate('clock_in_date', '<=', Carbon::now()->toDateString())
                ->get();

            // Get all the days of the current month up to today, excluding Saturdays and Sundays
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfDay();
            $allDays = [];

            while ($startDate->lte($endDate)) {
                // Skip Saturdays and Sundays
                if (!$startDate->isWeekend()) {
                    $allDays[] = $startDate->format('Y-m-d');
                }
                $startDate->addDay();
            }

            // Identify present and absent days
            $presentDays = $attendances->keyBy('clock_in_date')->toArray();
            $absentDays = array_diff($allDays, array_keys($presentDays));

            // Return the response
            return response()->json([
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    // public function checkIn(Request $request)
    // {
    //     $user = Auth::user();
    //     $employee_id = $user->employee_id; // Get employee_id from the authenticated user
    
    //     // Ensure employee_id is valid
    //     if (!$employee_id) {
    //         return response()->json([
    //             'message' => 'Employee ID not found'
    //         ], 404);
    //     }
    
    //     // Get current date
    //     $currentDate = Carbon::now()->toDateString();
    
    //     // Check if there's already a check-in record for today
    //     $existingAttendance = Attendance::where('employee_id', $employee_id)
    //         ->where('clock_in_date', $currentDate)
    //         ->whereNotNull('clock_in_time') // Ensure it's not null to identify actual check-ins
    //         ->first();
    
    //     if ($existingAttendance) {
    //         // Calculate the time difference in seconds between check-in time and current time
    //         $checkInTime = Carbon::parse($existingAttendance->clock_in_time);
    //         $currentTime = Carbon::now();
    //         $timeElapsedInSeconds = $currentTime->diffInSeconds($checkInTime);
    
    //         // Convert seconds into hours, minutes, and seconds
    //         $hours = floor($timeElapsedInSeconds / 3600);
    //         $minutes = floor(($timeElapsedInSeconds % 3600) / 60);
    //         $seconds = $timeElapsedInSeconds % 60;
    
    //         // Format the time as HH:MM:SS
    //         $timeElapsedFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    
    //         return response()->json([
    //             'message' => 'You have already checked in today.',
    //             'status' => 400,
    //             'attendance' => [
    //                 'employee_id' => $existingAttendance->employee_id,
    //                 'clock_in_date' => $existingAttendance->clock_in_date,
    //                 'clock_in_time' => $existingAttendance->clock_in_time,
    //                 'clock_out_date' => $existingAttendance->clock_out_date,
    //                 'clock_out_time' => $existingAttendance->clock_out_time,
    //                 'reason' => $existingAttendance->reason,
    //                 'time_pass' => $timeElapsedFormatted, // Return formatted time
    //             ]
    //         ], 400);
    //     }
    
    //     // Get current date and time from the system
    //     $currentDateTime = Carbon::now();
    //     $currentDate = $currentDateTime->toDateString();
    //     $currentTime = $currentDateTime->toTimeString();
    
    //     // Create a new attendance record for check-in
    //     $attendance = new Attendance();
    //     $attendance->employee_id = $employee_id;
    //     $attendance->clock_in_date = $currentDate;
    //     $attendance->clock_in_time = $currentTime;
    //     $attendance->clock_out_date = null;
    //     $attendance->clock_out_time = null;
    //     $attendance->reason = 'Online attendance';
        
    //     $attendance->save();
    
    //     return response()->json([
    //         'message' => 'Check-in successful',
    //         'status' => 200,
    //         'attendance' => [
    //             'employee_id' => $attendance->employee_id,
    //             'clock_in_date' => $attendance->clock_in_date,
    //             'clock_in_time' => $attendance->clock_in_time,
    //             'clock_out_date' => $attendance->clock_out_date,
    //             'clock_out_time' => $attendance->clock_out_time,
    //             'reason' => $attendance->reason,
    //             'time_pass' => '00:00:00' // Time passed for new check-ins is zero
    //         ]
    //     ], 200);
    // }
    
}
