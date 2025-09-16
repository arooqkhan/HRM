<?php

namespace App\Http\Controllers;

use App\Models\Rota;
use App\Models\Shift;
use App\Models\Employee;
use App\Exports\RotaExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RotaController extends Controller
{


    public function __construct()
    {
        $this->middleware('permission:view rota', ['only' => ['index']]);
        $this->middleware('permission:download rota', ['only' => ['download']]);
    }
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
        // Default to showing the current week
        $startDate = $today->copy()->startOfWeek();
        $endDate = $today->copy()->endOfWeek();
    }

    $allEmployees = Employee::all();

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

    // Pass the data to the view
    return view('admin.pages.rota.index', compact('employees', 'startDate', 'endDate', 'allEmployees'));
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






public function download(Request $request)
{
    $employeeId = $request->input('employee_id');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    $authUser = auth()->user()->employee_id;

    // Check the user role to restrict access
    if (!auth()->user()->hasRole(['admin', 'HR','Accountant']) && auth()->user()->employee_id != $employeeId) {
        $employees = $authUser ? Employee::where('id', $authUser)->with(['shifts', 'leaves'])->get()
                               : Employee::with(['shifts', 'leaves'])->get();
        return Excel::download(new RotaExport($employees), 'rota_data.csv');
    }

    // Retrieve employee data based on the selected ID and date range
    $query = Employee::with(['shifts' => function ($query) use ($startDate, $endDate) {
        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }
    }, 'leaves' => function ($query) use ($startDate, $endDate) {
        if ($startDate && $endDate) {
            $query->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate]);
            });
        }
    }]);

    if ($employeeId) {
        $query->where('id', $employeeId);
    }

    $employees = $query->get();

    // Generate and download the CSV file
    return Excel::download(new RotaExport($employees), 'rota_data.csv');
}








}
