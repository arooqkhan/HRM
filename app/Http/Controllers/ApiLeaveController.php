<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Mail\LeaveNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ApiLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if the authenticated user has the 'admin' role
        if (Auth::user()->role == 'admin') {
            // If the user is an admin, retrieve all leave records with employee details
            $leaves = Leave::with('employee:id,first_name,last_name')->get();
        } else {
            // If the user is not an admin, retrieve only the leave records associated with the logged-in user
            $leaves = Leave::with('employee:id,first_name,last_name')
                ->where('employee_id', Auth::user()->employee_id)
                ->get();
        }
    
        // Check if any leave records exist
        if ($leaves->isEmpty()) {
            // If no records are found, return a 404 response
            return response()->json(['message' => 'No leave records found.'], 404);
        } else {
            // Format the leave records to include the full name as "name"
            $formattedLeaves = $leaves->map(function($leave) {
                return [
                    'leave_id' => $leave->id,
                    'name' => $leave->employee->first_name . ' ' . $leave->employee->last_name,
                    'leave_type' => $leave->leave_type,
                    'start_date' => $leave->start_date,
                    'end_date' => $leave->end_date,
                    'status' => $leave->status,
                    'reason' => $leave->reason,
                    'duration' => $leave->age,
                    
                ];
            });
    
            // Return the formatted leave records
            return response()->json($formattedLeaves);
        }
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        // Retrieve the authenticated user's employee ID
        $employeeId = Auth::user()->employee_id;
        
    
        // Log the employee ID to confirm it exists
        Log::info('Attempting to create leave for employee:', ['employee_id' => $employeeId]);
        
        // Validate that the employee exists in the employees table
        $employeeExists = DB::table('employees')->where('id', $employeeId)->exists();
    
        if (!$employeeExists) {
            return response()->json(['message' => 'Invalid employee ID.'], 400);
        }
    
        // Validate the input data
        $request->validate([
            'leave_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:1000',
        ]);
        
        // Create the leave record
        $leave = Leave::create([
            'employee_id' => $employeeId, // Store the employee_id of the logged-in user
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'age' => $request->duration,
            'reason' => $request->reason,
        ]);
        
        $employee = Employee::find($employeeId); 
        $employeeName = $employee ? $employee->first_name . ' ' . $employee->last_name : 'Unknown Employee';
        // Send the email with the employee's name and leave details
        Mail::to('hamzakayani371@gmail.com')->send(new LeaveNotification($leave, $employeeName));
    
        // Return a success response
        return response()->json([
            'message' => 'Leave request submitted successfully.',
            'leave' => $leave,
        ], 201);
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Attempt to find the leave record for the authenticated user
        $leave = Leave::where('employee_id', Auth::user()->employee_id)->find($id);
    
        // Check if the leave record exists
        if (!$leave) {
            return response()->json(['message' => 'Leave request not found.'], 404);
        }
    
        // Delete the leave record
        $leave->delete();
    
        return response()->json(['message' => 'Leave request deleted successfully.']);
    }
}
