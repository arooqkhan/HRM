<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Expense;
use Nette\Schema\Expect;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
     {
         $this->middleware('permission:view expenses', ['only' => ['index']]);
         $this->middleware('permission:create expenses', ['only' => ['create','store']]);
         $this->middleware('permission:update expenses', ['only' => ['update','edit']]);
         $this->middleware('permission:delete expenses', ['only' => ['destroy']]);
         $this->middleware('permission:show expenses', ['only' => ['show']]);
     }
    public function index()
    {
        $expenses = Expense::orderBy('created_at', 'desc')->get();
        return view('admin.pages.expense.index',compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.pages.expense.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|digits_between:1,8', // Enforce maximum of 8 digits
            'date' => 'required|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Adjust validation as needed
        ]);
    
        // Handle the image upload
        $imagePath = null; // Default value
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_image.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $imagePath = 'images/' . $imageName;
        }
    
        // Create a new expense record
        $expense = new Expense();
        $expense->name = $validated['name'];
        $expense->price = $validated['price'];
        $expense->date = $validated['date'];
        $expense->image = $imagePath; // Store the image path
        $expense->save();
    
        // Redirect with a success message
        return redirect()->route('expenses.index')->with('success', 'Expense added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $expense = Expense::findOrFail($id);
        $period = request('period', 'daily'); // Default to 'daily' if no period is specified

        switch ($period) {
            case 'daily':
                $expenses = $this->getDailyExpenses();
                break;
            case 'weekly':
                $expenses = $this->getWeeklyExpenses();
                break;
            case 'monthly':
                $expenses = $this->getMonthlyExpenses();
                break;
            default:
                $expenses = $this->getDailyExpenses(); // Default to daily if invalid period
                break;
        }

        return view('admin.pages.expense.show', compact('expense', 'expenses', 'period'));
    }

    private function getDailyExpenses()
    {
        return Expense::whereDate('date', today())->get();
    }

    private function getWeeklyExpenses()
    {
        return Expense::whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->get();
    }

    private function getMonthlyExpenses()
    {
        return Expense::whereMonth('date', now()->month)->get();
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        return view('admin.pages.expense.edit', compact('expense'));
    }

    public function download($period)
{
    // Fetch expenses based on the selected period
    $expenses = Expense::whereBetween('date', $this->getPeriodRange($period))->get();
    
    // Generate PDF
    $pdf = Pdf::loadView('admin.pages.expense.pdf', ['expenses' => $expenses, 'period' => ucfirst($period)]);
    
    // Return the PDF as a download with the appropriate file name
    return $pdf->download('expenses_' . $period . '.pdf');
}
    
private function getPeriodRange($period)
{
    switch($period) {
        case 'daily':
            return [now()->startOfDay(), now()->endOfDay()];
        case 'weekly':
            return [now()->startOfWeek(), now()->endOfWeek()];
        case 'monthly':
            return [now()->startOfMonth(), now()->endOfMonth()];
        default:
            // Default to the daily range if period is invalid
            return [now()->startOfDay(), now()->endOfDay()];
    }
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    // Find the expense record
    $expense = Expense::findOrFail($id);

    // Update the expense details with the request data
    if ($request->has('name')) {
        $expense->name = $request->input('name');
    }
    if ($request->has('price')) {
        $expense->price = $request->input('price');
    }
    if ($request->has('date')) {
        $expense->date = $request->input('date');
    }

    // Handle the image upload
    if ($request->hasFile('image')) {
        // Delete the old image if it exists
        if ($expense->image && file_exists(public_path($expense->image))) {
            unlink(public_path($expense->image));
        }

        // Upload the new image
        $image = $request->file('image');
        $imageName = time() . '_image.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);
        $expense->image = 'images/' . $imageName;
    }

    // Save the updated expense record
    $expense->save();

    // Redirect with a success message
    return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
}

    
    
    
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Find the expense record
        $expense = Expense::findOrFail($id);
    
        // Delete the associated image if it exists
        if ($expense->image && file_exists(public_path($expense->image))) {
            unlink(public_path($expense->image));
        }
    
        // Delete the expense record
        $expense->delete();
    
        // Redirect with a success message
        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully!');
    }
}
