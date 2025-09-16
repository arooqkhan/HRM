<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shift;
use App\Models\Employee;
use App\Mail\AcceptShift;
use App\Mail\RejectShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ApiRotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the current date
        $today = \Carbon\Carbon::now();
    
        // Determine the date range based on the view (week, two weeks, month, or custom dates)
        if ($request->has('view')) {
            $view = $request->get('view');
            if ($view == 'two_weeks') {
                $startDate = $today->copy()->addWeeks(1)->startOfWeek();
                $endDate = $today->copy()->addWeeks(2)->endOfWeek();
            } elseif ($view == 'month') {
                $startDate = $today->copy()->startOfMonth();
                $endDate = $today->copy()->endOfMonth();
            } else {
                $startDate = $today->copy()->startOfWeek();
                $endDate = $today->copy()->endOfWeek();
            }
        } elseif ($request->has('start_date') && $request->has('end_date')) {
            // Custom date range
            $startDate = \Carbon\Carbon::parse($request->get('start_date'));
            $endDate = \Carbon\Carbon::parse($request->get('end_date'));
        } else {
            // Default to showing the current month
            $startDate = $today->copy()->startOfMonth();
            $endDate = $today->copy()->endOfMonth();
        }
    
        // Get the authenticated user
        $user = auth()->user();
    
        // Retrieve employees based on the user's role and selected employee
        $employees = Employee::with([
            'shifts' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])->orderBy('date');
            },
            'leaves' => function($query) use ($startDate, $endDate) {
                $query->where(function($q) use ($startDate, $endDate) {
                    // Include multi-day leaves (start_date to end_date) and single-day leaves (date)
                    $q->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function($q) use ($startDate, $endDate) {
                          // Overlapping multi-day leave
                          $q->where('start_date', '<=', $endDate)
                            ->where('end_date', '>=', $startDate);
                      })
                      ->orWhereBetween('date', [$startDate, $endDate]); // for single-day leaves
                });
            }
        ]);
    
        // Check if a specific employee is selected
        if ($request->filled('employee_id')) {
            $employees->where('id', $request->employee_id);
        }
    
        // Check if the user is restricted based on their role
        if (!in_array($user->role, ['admin', 'HR', 'Accountant'])) {
            $employees->where('id', $user->employee_id);
        }
    
        // Execute the query to get employee data
        $employees = $employees->get();
    
        // Format the response data
        $data = $employees->map(function($employee) use ($startDate, $endDate) {
            return [
                'employee_id' => $employee->id,
                'name' => $employee->first_name . ' ' . $employee->last_name,
                'shifts' => $employee->shifts->map(function($shift) {
                    return [
                        'id' => $shift->id,
                        'date' => $shift->date,
                        'shift_type' => $shift->shift_type,
                        'start_time' => $shift->start_time,
                        'end_time' => $shift->end_time,
                        'add_duty' => $shift->add_duty,
                        'note' => $shift->node,
                        'status' => $shift->status,
                    ];
                }),
                'leaves' => $employee->leaves->map(function($leave) {
                    if ($leave->start_date && $leave->end_date && $leave->start_date != $leave->end_date) {
                        // Multi-day leave
                        return [
                            'leave_type' => $leave->leave_type,
                            'reason' => $leave->reason,
                            'start_date' => $leave->start_date,
                            'end_date' => $leave->end_date,
                            'is_single_day' => false,
                        ];
                    } elseif ($leave->date || ($leave->start_date && $leave->start_date == $leave->end_date)) {
                        // Single-day leave
                        return [
                            'leave_type' => $leave->leave_type,
                            'reason' => $leave->reason,
                            'date' => $leave->date ?? $leave->start_date, // Show single-day as date
                            'is_single_day' => true,
                        ];
                    }
                })->filter()->values(), // Filters out null entries and resets array indices
            ];
        });
    
        // Return data in JSON format
        return response()->json([
            'data' => $data,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
        ]);
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
        //
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
    public function destroy(string $id)
    {
        //
    }


    public function acceptShift($id)
    {
        // Find the shift by its ID
        $shift = Shift::find($id);
    
        // Check if the shift exists
        if (!$shift) {
            return response()->json(['error' => 'Shift not found'], 404);
        }
    
        // Check if the shift is already accepted
        if ($shift->status == 1) {
            return response()->json(['error' => 'Shift is already accepted'], 400);
        }
    
        // Update the shift's status to accepted (1)
        $shift->status = 1;
        $shift->save();
    
        // Get the user who created the shift
        $user = User::find($shift->user_id);
    
        // Send an email to the user (if the user exists)
        if ($user) {
            Mail::to($user->email)->send(new AcceptShift($shift));
        }
    
        return response()->json(['status' => 'accepted', 'shift_id' => $id]);
    }



public function rejectShift($id)
{
    // Find the shift by its ID
    $shift = Shift::find($id);

    // Check if the shift exists
    if (!$shift) {
        return response()->json(['error' => 'Shift not found'], 404);
    }

    // Check if the shift is already rejected
    if ($shift->status == 2) {
        return response()->json(['error' => 'Shift is already rejected'], 400);
    }

    // Update the shift's status to rejected (2)
    $shift->status = 2;
    $shift->save();

    // Get the user who created the shift
    $user = User::find($shift->user_id);

    // Send an email to the user (if the user exists)
    if ($user) {
        Mail::to($user->email)->send(new RejectShift($shift));
    }

    return response()->json(['status' => 'rejected', 'shift_id' => $id]);
}


}
