<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Mail\EmployeeRegistered;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


use Illuminate\Support\Facades\Storage;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('permission:view employee', ['only' => ['index']]);
        $this->middleware('permission:create employee', ['only' => ['create','store']]);
        $this->middleware('permission:update employee', ['only' => ['update','edit']]);
        $this->middleware('permission:delete employee', ['only' => ['destroy']]);
        $this->middleware('permission:show employee', ['only' => ['show']]);
    }

public function index()
{
    // Retrieve the logged-in user's role
    $user = auth()->user();
    
    if ($user->role === 'admin' || $user->role ==='HR' || $user->role ==='Accountant') {
        // If the user is an admin, show all employees with pagination
        $employees = Employee::orderBy('created_at', 'desc')->paginate(9);
    } else {
        // If the user is not an admin, show only the logged-in user's employee record
        $employees = Employee::where('id', $user->employee_id)->paginate(9);
    }

    // Pass the employees collection to the view
    return view('admin.pages.employee.index', compact('employees'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    // Fetch the highest employee_id from the employees table
    $maxEmployeeId = DB::table('employees')
        ->select(DB::raw('MAX(CAST(SUBSTRING(employee_id, 4, LENGTH(employee_id) - 3) AS UNSIGNED)) as max_id'))
        ->value('max_id');

    // Generate the next employee_id
    $nextEmployeeId = 'EMP' . str_pad(($maxEmployeeId + 1), 2, '0', STR_PAD_LEFT);

    return view('admin.pages.employee.create', compact('nextEmployeeId'));
}

    /**
     * Store a newly created resource in storage.
     */

     
     public function store(Request $request)
     { 
         // Validate the incoming request data
         $validatedData = $request->validate([
             'first_name' => 'required|string|max:30',
             'last_name' => 'required|string|max:30',
             'email' => 'required|email|unique:users,email',
             'contact_email' => 'required|email|unique:employees,contact_email',
             'gender' => 'required|in:male,female,other',
             'employee_id' => 'required|string|max:255|unique:employees,employee_id',
             'department' => 'required|string|max:255',
             'designation' => 'required|string|max:255',
             'employee_status' => 'required|string|max:255',
             'role' => 'required|string|max:255',
             'salary' => 'required|numeric|min:0',
             'number' => 'required|string|min:0',
             'emgr_number' => 'required|string|min:0', 
             'joining_date' => 'required|date',
             'work_shift' => 'required|string|max:255',
             'dob' => 'required|date',
             'ninumber' => 'required|string|max:15|unique:employees,ninumber',
             'address' => 'required|string|max:500',
             'visa' => 'nullable',
             'visadate' => 'nullable',
             'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
             'password' => 'required|string|min:8',
             'documents' => 'nullable|array',
             'documents.*' => 'string|max:255',
         ]);
     
         // Handle image upload if provided
         $imagePath = null;
         if ($request->hasFile('image')) {
             $image = $request->file('image');
             $imageName = time() . '_image.' . $image->getClientOriginalExtension();
             $image->move(public_path('images'), $imageName);
             $imagePath = 'images/' . $imageName;
         }
     
         // Create the employee record in the employees table
         $employeeData = array_merge($validatedData, [
            'image' => $imagePath,
            'documents' => json_encode($request->documents),
        ]);
         $employeeData = Arr::except($employeeData, ['email']);
         $employee = Employee::create($employeeData);
     
         // Create the user record in the users table
         $userData = [
             'employee_id' => $employee->id,
             'name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
             'email' => $validatedData['email'],
             'password' => Hash::make($validatedData['password']),
             'role' => $validatedData['role'],
             'image' => $imagePath,
         ];
         $user = User::create($userData);
 
         $role = Role::where('name', $request->role)->first();
         $user->assignRole($role);
 
         // Update the employee record with the user_id
         $employee->update(['user_id' => $user->id]);
          
         
         $orgpassword = $request->password;
        
         // send email
         Mail::to($validatedData['contact_email'])->send(new EmployeeRegistered($employee, $validatedData['email'], $orgpassword));
 
     
         // Redirect back with a success message
         return redirect()->route('employee.index')->with('success', 'Employee created successfully.');
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
    public function edit($id)
    {
        // Fetch the employee and eager load the associated user
        $employee = Employee::with('user')->findOrFail($id);
    
        
        return view('admin.pages.employee.edit', compact('employee'));
    }
    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $id)
    // {
    //     // Find the employee by ID
    //     $employee = Employee::findOrFail($id);
    
    //     // Validate the incoming request data
    //     $validatedData = $request->validate([
    //         'first_name' => 'required|string|max:255',
    //         'last_name' => 'required|string|max:255',
    //         'email' => 'required|email', // Removed unique validation
    //         'contact_email' => 'required|email', // Removed unique validation
    //         'gender' => 'required|in:male,female,other',
    //         'employee_id' => 'required|string|max:255|unique:employees,employee_id,' . $employee->id,
    //         'department' => 'required|string|max:255',
    //         'designation' => 'required|string|max:255',
    //         'employee_status' => 'required|string|max:255',
    //         'role' => 'required|string|max:255',
    //         'salary' => 'required|numeric|min:0',
    //         'number' => 'required|numeric|min:0',
    //         'emgr_number' => 'required|numeric|min:0',
    //         'joining_date' => 'required|date',
    //         'work_shift' => 'required|string|max:255',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //         'password' => 'nullable|string|min:8', // Password is optional during update
    //         'dob' => 'required|date',
    //         'address' => 'required|string|max:255',
    //         'cnic' => 'required|string|max:15',
    //     ]);
    
    //     // Handle the profile image upload if provided
    //     if ($request->hasFile('image')) {
    //         // Delete the old image if it exists in both tables
    //         if ($employee->image) {
    //             $oldImagePath = public_path($employee->image);
    //             if (file_exists($oldImagePath)) {
    //                 unlink($oldImagePath);
    //             }
    //         }
    //         if ($employee->user && $employee->user->image) {
    //             $oldImagePath = public_path($employee->user->image);
    //             if (file_exists($oldImagePath)) {
    //                 unlink($oldImagePath);
    //             }
    //         }
    
    //         // Store the new image
    //         $image = $request->file('image');
    //         $imageName = time() . '_image.' . $image->getClientOriginalExtension();
    //         $image->move(public_path('images'), $imageName);
    //         $validatedData['image'] = 'images/' . $imageName;
    //     } else {
    //         // If no new image is uploaded, keep the old image path
    //         $validatedData['image'] = $employee->image;
    //     }
    
    //     // Update the employee record
    //     $employeeData = Arr::except($validatedData, ['email']); // Remove email from employee data
    //     $employee->update($employeeData);
    
    //     // Update the user record if exists
    //     if ($employee->user) {
    //         $userData = [
    //             'email' => $validatedData['email'],
    //             'name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
    //             'role' => $validatedData['role'],
    //             'image' => $validatedData['image'], // Update image in the user table as well
    //         ];
    
    //         // Update password if provided
    //         if ($request->filled('password')) {
    //             $userData['password'] = Hash::make($validatedData['password']);
    //         }
    
    //         $employee->user->update($userData);
    //     }
    
    //     // Redirect back with a success message
    //     return redirect()->route('employee.index')->with('success', 'Employee updated successfully.');
    // }


    public function update(Request $request, $id)
{
    // Find the employee by ID
    $employee = Employee::findOrFail($id);

    // Validate the incoming request data
    $validatedData = $request->validate([
        'first_name' => 'sometimes|string|max:255',
        'last_name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email', // Removed unique validation
        'contact_email' => 'sometimes|email', // Removed unique validation
        'gender' => 'sometimes|in:male,female,other',
        'employee_id' => 'sometimes|string|max:255|unique:employees,employee_id,' . $employee->id,
        'department' => 'sometimes|string|max:255',
        'designation' => 'sometimes|string|max:255',
        'employee_status' => 'sometimes|string|max:255',
        'role' => 'sometimes|string|max:255',
        'salary' => 'sometimes|numeric|min:0',
        'number' => 'sometimes|string|min:0',
        'emgr_number' => 'sometimes|string|min:0',
        'joining_date' => 'sometimes|date',
        'work_shift' => 'sometimes|string|max:255',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'password' => 'nullable|string|min:8', // Password is optional during update
        'dob' => 'sometimes|date',
        'address' => 'sometimes|string|max:255',
        'ninumber' => 'sometimes|string|max:15',
        'visa' => 'nullable',
        'visadate' => 'nullable',
    ]);

    // Handle the profile image upload if provided
    if ($request->hasFile('image')) {
        // Delete the old image if it exists
        if ($employee->image) {
            $oldImagePath = public_path($employee->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        if ($employee->user && $employee->user->image) {
            $oldImagePath = public_path($employee->user->image);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Store the new image
        $image = $request->file('image');
        $imageName = time() . '_image.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);
        $validatedData['image'] = 'images/' . $imageName;
    } else {
        // Keep the old image if no new image is uploaded
        $validatedData['image'] = $employee->image;
    }

    // Update the employee record
    $employeeData = Arr::except($validatedData, ['email']); // Remove email from employee data
    $employee->update($employeeData);

    // Update the user record if exists
    if ($employee->user) {
        $userData = [
            'email' => $validatedData['email'],
            'name' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
            'image' => $validatedData['image'],
            'role' => $validatedData['role'],
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($validatedData['password']);
        }

        $employee->user->update($userData);

        // Update the role
        $role = Role::where('name', $request->role)->first();
        if ($role) {
            $employee->user->syncRoles($role); // Syncs the user role
        }
    }

    // Redirect back with a success message
    return redirect()->route('employee.index')->with('success', 'Employee updated successfully.');
}

    
    
    
    
    
    


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
{
    $employee = Employee::findOrFail($id);
    
    // If the employee has a profile picture, delete it from storage
    if ($employee->profile_picture) {
        Storage::delete($employee->profile_picture);
    }

    $employee->delete();

    return redirect()->route('employee.index')->with('success', 'Employee deleted successfully');
}

}
