<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ApiShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     // Get the role of the logged-in user
    //     $role = auth()->user()->role;
    
    //     // Retrieve shifts based on the user's role
    //     if (in_array($role, ['admin', 'HR', 'Accountant'])) {
    //         // If the user has one of these roles, retrieve all shifts
    //         $shifts = Shift::select('shifts.id', 'shift_type', 'add_duty', 'date', 'start_time', 'end_time', 'status','node')
    //             ->join('employees', 'shifts.employee_id', '=', 'employees.id')
    //             ->join('users', 'shifts.user_id', '=', 'users.id')
    //             ->addSelect('employees.first_name', 'employees.last_name', 'users.name as user_name')
    //             ->orderBy('shifts.created_at', 'desc')
    //             ->get();
    //     } else {
    //         // For other users, retrieve only their shifts
    //         $shifts = Shift::select('shifts.id', 'shift_type', 'add_duty', 'date', 'start_time', 'end_time', 'status','node')
    //             ->join('employees', 'shifts.employee_id', '=', 'employees.id')
    //             ->join('users', 'shifts.user_id', '=', 'users.id')
    //             ->where('shifts.employee_id', auth()->user()->employee_id)
    //             ->addSelect('employees.first_name', 'employees.last_name', 'users.name as user_name')
    //             ->orderBy('shifts.created_at', 'desc')
    //             ->get();
    //     }
    
    //     // Check if no data is found
    //     if ($shifts->isEmpty()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No Data Found'
    //         ], 404);
    //     }
    
    //     // Format the response data
    //     $formattedShifts = $shifts->map(function ($shift) {
    //         return [
    //             'id' => $shift->id,
    //             'shift_type' => $shift->shift_type,
    //             'add_duty' => $shift->add_duty,
    //             'date' => $shift->date,
    //             'start_time' => $shift->start_time,
    //             'end_time' => $shift->end_time,
    //             'note' => $shift->node,
    //             'status' => $shift->status == 0 ? 'pending' : ($shift->status == 1 ? 'accepted' : 'rejected'),
    //             'employee_name' => $shift->first_name . ' ' . $shift->last_name,
    //             'user_name' => $shift->user_name, 
    //         ];
    //     });
    
    //     // Return the formatted shifts as JSON response
    //     return response()->json([
    //         'success' => true,
    //         'shifts' => $formattedShifts
    //     ], 200);
    // }


    public function index()
    {
        // Get the role of the logged-in user
        $role = auth()->user()->role;
    
        // Get the current month and year
        $currentMonth = now()->month;
        $currentYear = now()->year;
    
        // Retrieve shifts based on the user's role and filter by the current month
        if (in_array($role, ['admin', 'HR', 'Accountant'])) {
            // Admin, HR, or Accountant: Retrieve all shifts for the current month
            $shifts = Shift::select('shifts.id', 'shift_type', 'add_duty', 'date', 'start_time', 'end_time', 'status', 'node')
                ->join('employees', 'shifts.employee_id', '=', 'employees.id')
                ->join('users', 'shifts.user_id', '=', 'users.id')
                ->addSelect('employees.first_name', 'employees.last_name', 'users.name as user_name')
                ->whereMonth('shifts.date', $currentMonth)
                ->whereYear('shifts.date', $currentYear)
                ->orderBy('shifts.created_at', 'desc')
                ->get();
        } else {
            // Other users: Retrieve only their shifts for the current month
            $shifts = Shift::select('shifts.id', 'shift_type', 'add_duty', 'date', 'start_time', 'end_time', 'status', 'node')
                ->join('employees', 'shifts.employee_id', '=', 'employees.id')
                ->join('users', 'shifts.user_id', '=', 'users.id')
                ->where('shifts.employee_id', auth()->user()->employee_id)
                ->whereMonth('shifts.date', $currentMonth)
                ->whereYear('shifts.date', $currentYear)
                ->addSelect('employees.first_name', 'employees.last_name', 'users.name as user_name')
                ->orderBy('shifts.created_at', 'desc')
                ->get();
        }
    
        // Check if no data is found
        if ($shifts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No Data Found for the Current Month'
            ], 404);
        }
    
        // Format the response data
        $formattedShifts = $shifts->map(function ($shift) {
            return [
                'id' => $shift->id,
                'shift_type' => $shift->shift_type,
                'add_duty' => $shift->add_duty,
                'date' => $shift->date,
                'start_time' => $shift->start_time,
                'end_time' => $shift->end_time,
                'note' => $shift->node,
                'status' => $shift->status == 0 ? 'pending' : ($shift->status == 1 ? 'accepted' : 'rejected'),
                'employee_name' => $shift->first_name . ' ' . $shift->last_name,
                'user_name' => $shift->user_name,
                
            ];
        });
    
        // Return the formatted shifts as JSON response
        return response()->json([
            'shifts' => $formattedShifts,
            'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->endOfMonth()->toDateString(),
        ], 200);
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
}
