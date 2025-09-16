<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Mail\LeaveNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('permission:view leave', ['only' => ['index']]);
        $this->middleware('permission:create leave', ['only' => ['create', 'store']]);
        $this->middleware('permission:update leave', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete leave', ['only' => ['destroy']]);
        $this->middleware('permission:show leave', ['only' => ['show']]);
        $this->middleware('permission:status leave', ['only' => ['accept', 'reject']]);
    }

    public function index()
    {
        $user = auth()->user();
        $currentMonth = now()->month;
        $currentYear = now()->year;
    
        if ($user->role === 'admin' || $user->role === 'HR' || $user->role === 'Accountant') {
            // Admin can see all leaves for the current month
            $leaves = Leave::with('employee')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Employee can see only their own leaves for the current month
            $leaves = Leave::with('employee')
                ->where('employee_id', $user->employee_id)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    
        // Determine if the leave is single or multi-day
        foreach ($leaves as $leave) {
            // Check for single-day leave using the 'date' field
            if ($leave->date) {
                $leave->leave_days = 1;
            } 
            // If it's not a single-day leave, check for multi-day using 'start_date' and 'end_date'
            elseif ($leave->start_date && $leave->end_date) {
                // Convert start_date and end_date to Carbon instances
                $startDate = Carbon::parse($leave->start_date);
                $endDate = Carbon::parse($leave->end_date);
                
                $leave->leave_days = $startDate->diffInDays($endDate) + 1;
            } 
            // If neither single nor multi-day information is available, set leave_days to null
            else {
                $leave->leave_days = null;
            }
        }
    
        return view('admin.pages.leave.index', compact('leaves'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user(); // Get the currently authenticated user

        if ($user->role === 'admin') {
            // Admin sees all employees
            $employees = Employee::all(['id', 'first_name', 'last_name']);
        } else {
            // Non-admin sees all employees, including themselves
            $employees = Employee::whereIn('id', [$user->employee_id])
                ->orWhere('id', $user->employee_id)
                ->get(['id', 'first_name', 'last_name']);
        }

        return view('admin.pages.leave.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        // Create new Leave instance and populate it with request data
        $leave = new Leave();
        $leave->employee_id = $request->employee_id;
        $leave->leave_type = $request->leave_type;
        $leave->age = $request->duration;
        $leave->reason = $request->reason;
        $leave->start_date = $request->start_date;
        $leave->end_date = $request->end_date;
        $leave->start_time = $request->start_time;
        $leave->end_time = $request->end_time;
        $leave->date = $request->date;
        $leave->first_half = $request->leave_duration == 'First half' ? 1 : 0;
        $leave->last_half = $request->leave_duration == 'Second half' ? 1 : 0;


        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_image.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $leave->image = 'images/' . $imageName;
        }


        $leave->save();

        // Get the employee's name using employee_id
        $employee = Employee::find($request->employee_id);
        $employeeName = $employee ? $employee->first_name . ' ' . $employee->last_name : 'Unknown Employee';



        // Send the email with the employee's name and leave details
        Mail::to('hamzakayani371@gmail.com')->send(new LeaveNotification($leave, $employeeName));

        // Redirect with success message
        return redirect()->route('leave.index')->with('success', 'Leave request submitted successfully.');
    }


    public function accept($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->status = 1; // Set status to Accepted
        $leave->save();

        return redirect()->back()->with('success', 'Leave request accepted.');
    }

    public function reject($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->status = 2; // Set status to Rejected
        $leave->save();

        return redirect()->back()->with('success', 'Leave request rejected.');
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
        $leave = Leave::findOrFail($id);

        $leave->delete();

        return redirect()->route('leave.index')->with('success', 'Leave Cancel successfully');
    }
}
