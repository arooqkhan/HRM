<?php

namespace App\Http\Controllers;

use App\Mail\AcceptShift;
use App\Mail\RejectShift;
use App\Mail\Shift as ShiftMail;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Models\Shift;

class ShiftController extends Controller
{

    public function __construct()
     {
         $this->middleware('permission:view shift', ['only' => ['index']]);
         $this->middleware('permission:create shift', ['only' => ['create','store']]);
         $this->middleware('permission:update shift', ['only' => ['update','edit']]);
         $this->middleware('permission:delete shift', ['only' => ['destroy']]);
         $this->middleware('permission:show shift', ['only' => ['show']]);
     }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shifts = Shift::with('employee')->get();

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
        $user = auth()->user();
    
        // Validate the incoming request data
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_type' => 'required|string',
            'add_duty' => 'required|string',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);
    
        // Create the shift using the validated data
        $shift = Shift::create($validatedData);
    
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
        $shift->status = 1; // Status 1 for accepted
        $shift->save();
        Mail::to('farooqbsse@gmail.com')->send(new AcceptShift($shift));
        return response()->json(['status' => 'accepted']);
    }
    return response()->json(['error' => 'Shift not found'], 404);
}

public function rejectShift($id)
{
    $shift = Shift::find($id);
    if ($shift) {
        $shift->status = 2; // Status 2 for rejected
        $shift->save();
        Mail::to('farooqbsse@gmail.com')->send(new RejectShift($shift));
        return response()->json(['status' => 'rejected']);
    }
    return response()->json(['error' => 'Shift not found'], 404);
}


}
