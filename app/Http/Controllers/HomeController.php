<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use App\Models\PayRoll;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\AccouncementDocument;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;


class HomeController extends Controller
{
    
    public function redirect()
    {
      
        $user = Auth::user();

        if ($user) {

                return view('admin.master.main');
            } else {
                return redirect()->route('admin.login');
            }

    } 


        public function abc(Request $request)
    {
        if (Auth::check()) {
            return view('admin.master.main');
        } else {
            return redirect()->route('admin.login');
        }
    }

    public function abcGet(Request $request)
    {
        if (Auth::check()) {
            return view('admin.master.main');
        } else {
            return redirect()->route('admin.login');
        }
    }
    

    
    public function login(Request $request)
    {
      
      
        $user = User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {

            Auth::login($user);
            return redirect()->route('dashboard');
        }

        return redirect()->back()->with('error', 'Email and Password is Incorrect.');
    }

    public function reset(Request $request)
    {
        // Validate the request
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'password_confirmation' => 'required',
        ]);
        // Attempt password reset
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Log user information for debugging
                Log::info('User ID: ' . $user->id);
                Log::info('User Email: ' . $user->email);
                // Hash and update the password
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->save();
                // You can perform additional actions after password reset, like logging in the user
                // auth()->login($user);
            }
        );
        // Log the password reset status
        Log::info('Password reset status: ' . $status);
        // Redirect the user based on the password reset status
        return $status == Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('success', 'Password update successful. Please login.')
            : back()->with('error', 'Password update failed. Please try again.');
    }


    public function registerform()
    {
        return view('auth.register');
    }


    public function registergata(Request $request)
    {
     
       
        // Validate the request 
        $request->validate([
            'name' => 'required|string|max:255',

            'email' => 'required|string|email|max:255|unique:users,email', // Check uniqueness in the "users" table
            'password' => 'required|string|min:8',
        ]);
        try {
            // Create a new user instance
            $user = new User();
            $user->name = $request->name;
        
            $user->email = $request->email;
            $user->password = bcrypt($request->password);

            // Save the user
            $user->save();

            // Redirect to the login page
            return redirect()->route('admin.login')->with('success', 'Registration successful. Please login.');
        } catch (\Exception $e) {
            // If an exception occurs, it might be due to a duplicate email entry
            return redirect()->back()->withErrors(['email' => 'The email address is already registered.'])->withInput();
        }
    }



    public function log()
    {
       
       
        return view('auth.login');
    }

    public function log2()
    {
       
        
        return view('auth.login');
    }

    public function showForgotPasswordForm()
    {
        
        return view('auth.forgot-password');
    }

    public function resetPasswordForm()
    {
        // $set = Setting::firstOrNew();
        return view('auth.reset-password');
    }

    public function logout()

    {
        
        Auth::logout();

        return redirect()->route('admin.login');
    }

    public function updateProfile()
{
    // Check if user is authenticated
    if (Auth::check()) {
        // Get the authenticated user
        $user = Auth::user();
        return view('admin.pages.updateuser.updateprofile', ['user' => $user]);
    }
    
    // Handle case where user is not authenticated
    return redirect()->route('login'); // Redirect to login page
}


public function update(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $user = Auth::user();
    $user->name = $request->input('name');
    $user->email = $request->input('email');

    if ($request->hasFile('image')) {
        // Delete the old image if exists
        if ($user->image && file_exists(public_path($user->image))) {
            unlink(public_path($user->image));
        }

        $image = $request->file('image');
        $imageName = time() . '_image.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);
        $user->image = 'images/' . $imageName;
    }

    $user->save();

    return redirect()->route('profile.update')->with('success', 'Profile updated successfully.');
}




public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required|string|min:8',
        'new_password' => 'required|string|min:8|different:current_password',
        'new_password_confirmation' => 'required|string|same:new_password',
    ], [
        'current_password.required' => 'Please enter your current password.',
        'current_password.min' => 'Your current password must be at least :min characters.',
        'new_password.required' => 'Please enter a new password.',
        'new_password.min' => 'Your new password must be at least :min characters.',
        'new_password.different' => 'Your new password must be different from your current password.',
        'new_password_confirmation.required' => 'Please confirm your new password.',
        'new_password_confirmation.same' => 'The new password confirmation does not match.',
    ]);

    $user = Auth::user();

    // Check if the provided current password matches the user's current password
    if (!Hash::check($request->current_password, $user->password)) {
        return redirect()->back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
    }

    // Update the user's password
    $user->password = Hash::make($request->new_password);
    $user->save();

    return redirect()->back()->with('success', 'Password updated successfully.');
}


public function dashboard()
{

    // Get total count of employees
    $totalEmployees = Employee::count(); 
    
    // Get the total sum of salaries
    $totalSalary = Employee::sum('salary');

    $totalExpense = Expense::sum('price');
    
    // Get today's date
    $today = Carbon::today()->toDateString();
    
    // Count distinct employee_id records where created_at is today's date
    $totalPresent = Attendance::whereDate('created_at', $today)
        ->distinct('employee_id')
        ->count('employee_id');
    
    // Get the IDs of employees who are present today
    $presentEmployeeIds = Attendance::whereDate('created_at', $today)
        ->distinct('employee_id')
        ->pluck('employee_id');
    
    // Count distinct employees where the created_at date is not today
    $totalAbsent = Employee::whereNotIn('id', $presentEmployeeIds)->count();

    // Get the bonus for the logged-in employee
    $bonus = Payroll::where('employee_id', Auth::user()->employee_id)->value('bonus');

    // Get the start of the current month and today
    $monthStart = Carbon::now()->startOfMonth();
    $today = Carbon::today();

    // Get all workdays from the start of the month to today, excluding weekends
    $allDays = CarbonPeriod::create($monthStart, $today);
    $workDays = [];
    foreach ($allDays as $date) {
        if (!in_array($date->dayOfWeek, [0, 6])) { // Exclude Sunday (0) and Saturday (6)
            $workDays[] = $date->toDateString();
        }
    }

    // Initialize absent count
    $absentCount = 0;

    // Get the attendance dates for the logged-in employee
    $attendanceDates = Attendance::where('employee_id', Auth::user()->employee_id)
        ->whereBetween('clock_in_date', [$monthStart->toDateString(), $today->toDateString()])
        ->pluck('clock_in_date')
        ->map(function ($date) {
            return Carbon::parse($date)->toDateString();
        })
        ->toArray();

    // Prepare data for the chart
    $absenceGraphData = [];
    foreach ($workDays as $day) {
        $absent = !in_array($day, $attendanceDates) ? 1 : 0;
        $present = in_array($day, $attendanceDates) ? 1 : 0;
        $absenceGraphData[] = [
            'date' => $day,
            'absent' => $absent,
            'present' => $present
        ];

        if ($absent) {
            $absentCount++;
        }
    }


    // Get the list of present employees with their names
    $presentEmployees = Employee::whereIn('id', $presentEmployeeIds)->get();
    $absentEmployees = Employee::whereNotIn('id', $presentEmployeeIds)->get();
    
    //Get latest five Announcement
    $announcements = Announcement::latest()->take(5)->where('status',0)->get();

    $announcementdocuments = AccouncementDocument::where('employee_id', auth()->id())
    ->oRwhere('status', 0)
    ->latest() 
    ->take(5) 
    ->get();

    
    $currentWeekStart = Carbon::now()->startOfWeek();
    $currentWeekEnd = Carbon::now()->endOfWeek();
    $shift = Shift::with('employee')
    ->whereBetween('date', [$currentWeekStart, $currentWeekEnd])
    ->paginate(5);


    $currentMonth = Carbon::now()->month;
    $currentYear = Carbon::now()->year;
    
    $totalExpense = Expense::whereMonth('created_at', $currentMonth)
        ->whereYear('created_at', $currentYear)
        ->sum('price');



        $user = Auth::user()->employee_id;

// Fetch the employee details for the logged-in user
$employee = Employee::where('id', $user)->first();

if (!$employee) {
    return redirect()->route('login')->with('error', 'Employee information not found.');
}

$cho = now()->addHours(24); // Add 24 hours to the current time

// Check if created_at is more than 24 hours ago from now
$documents = ($employee->created_at && $employee->created_at->diffInHours(now()) >= 24) ? [] : ($employee->documents ? json_decode($employee->documents, true) : []);



    

 


  $empdoc = AccouncementDocument::where('employee_id',$user)
  ->where('status',0)
  ->first();


  $today = Carbon::today();

// Get the date 5 days from now
$nextFiveDays = Carbon::today()->addDays(5);

// Fetch employees whose `visadate` is within the next 5 days or earlier
$nxtRgtDate = Employee::where('visadate', '<=', $nextFiveDays)
                       ->where('visadate', '>=', $today)
                       ->get();
      

                      



 

    // Return data to the view
    return view('admin.master', compact(
        'totalEmployees', 
        'totalSalary', 
        'totalExpense', 
        'totalPresent', 
        'totalAbsent', 
        'bonus', 
        'absentCount', 
        'absenceGraphData', 
        'presentEmployees',
        'absentEmployees',
        'announcements',
        'announcementdocuments',
        'shift',
        'documents',
        'empdoc',
        'nxtRgtDate'
    ));

    
}


public function show($id)
{
    // Find the announcement by ID or fail
    $announcement = Announcement::findOrFail($id);

    // Update status for the specific announcement
    $announcement->status = 1;
    $announcement->save();

    // Pass the single announcement data to the view
    return view('admin.pages.announcement.detail', compact('announcement'));
}










}
