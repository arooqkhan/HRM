<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Shift;
use App\Models\Employee;

use App\Mail\AcceptShift;
use App\Mail\RejectShift;
use Illuminate\Http\Request;

use App\Mail\Shift as ShiftMail;
use Illuminate\Support\Facades\Mail;

class ShiftController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view shift', ['only' => ['index']]);
        $this->middleware('permission:create shift', ['only' => ['create', 'store']]);
        $this->middleware('permission:update shift', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete shift', ['only' => ['destroy']]);
        $this->middleware('permission:show shift', ['only' => ['show']]);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get the role of the logged-in user
        $role = auth()->user()->role;

        // If the user is admin, HR, or Accountant, retrieve all shifts
        if (in_array($role, ['admin', 'HR', 'Accountant'])) {
            $shifts = Shift::with('employee')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {


            $shifts = Shift::with('employee')
                ->where('employee_id', auth()->user()->employee_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('admin.pages.shift.index', compact('shifts'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::all();
        return view('admin.pages.shift.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $user = auth()->user(); // Get the currently authenticated user

    // Validate the incoming request data
    $validatedData = $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'shift_type' => 'required|string',
        'add_duty' => 'required|string',
        'date' => 'required|date',
        'node' => 'nullable',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i',
    ]);

    // Create the shift using the validated data and add the user_id (authenticated user's id)
    $shift = Shift::create(array_merge($validatedData, ['user_id' => $user->id]));

    // Send the email notification to the employee's contact email
    Mail::to($user->employee->contact_email)->send(new ShiftMail($shift));

    // Redirect with a success message
    return redirect()->route('shift.index')->with('success', 'Shift created successfully!');
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
        $shift = Shift::findOrFail($id);
        $employees = Employee::all();
        return view('admin.pages.shift.edit', compact('shift', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {




        $shift = Shift::findOrFail($id);


        $shift->employee_id = $request->input('employee_id');
        $shift->shift_type = $request->input('shift_type');
        $shift->add_duty = $request->input('add_duty');
        $shift->date = $request->input('date');
        $shift->start_time = $request->input('start_time');
        $shift->end_time = $request->input('end_time');
        $shift->node = $request->input('node');

        // Save the updated shift
        $shift->save();

        // Redirect with a success message
        return redirect()->route('shift.index')->with('success', 'Shift updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();
        return redirect()->route('shift.index')->with('success', 'Shift deleted');
    }

    public function acceptShift($id)
    {
        $shift = Shift::find($id);
    
        if ($shift) {
            // Update the status of the shift
            $shift->status = 1; 
            $shift->save();
    
            // Get the user who created the shift (based on user_id)
            $user = User::find($shift->user_id);  // Find the user by user_id

           
    
            if ($user) {
                // Send the email to the user who created the shift
                Mail::to($user->email)->send(new AcceptShift($shift));
            }
    
            return response()->json(['status' => 'accepted']);
        }
    
        return response()->json(['error' => 'Shift not found'], 404);
    }
    

    public function rejectShift($id)
{
    $shift = Shift::find($id);

    if ($shift) {
        // Update the status of the shift to rejected (status = 2)
        $shift->status = 2;
        $shift->save();

        // Get the user who created the shift (based on user_id)
        $user = User::find($shift->user_id);  // Find the user by user_id

        if ($user) {
            // Send the email to the user who created the shift
            Mail::to($user->email)->send(new RejectShift($shift));
        }

        return response()->json(['status' => 'rejected']);
    }

    return response()->json(['error' => 'Shift not found'], 404);
}

}
