<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view document', ['only' => ['index']]);
        $this->middleware('permission:create document', ['only' => ['create','store']]);
        $this->middleware('permission:update document', ['only' => ['update','edit']]);
        $this->middleware('permission:delete document', ['only' => ['destroy']]);
        $this->middleware('permission:show document', ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        // Retrieve documents based on user role
        if (auth()->user()->role == 'admin' || auth()->user()->role == 'HR' || auth()->user()->role == 'Accountant') {
            // Admin retrieves all documents with associated employee details
            $documents = Document::leftJoin('employees', 'documents.employee_id', '=', 'employees.id')
                ->select('documents.*', 'employees.first_name as employee_first_name', 'employees.last_name as employee_last_name', 'employees.image as employee_image')
                ->orderBy('documents.created_at', 'desc')
                ->get();
        } else {
            // Non-admins (employees) should see:
            // - Their own documents, or
            // - Documents with status 1
            $documents = Document::leftJoin('employees', 'documents.employee_id', '=', 'employees.id')
                ->select('documents.*', 'employees.first_name as employee_first_name', 'employees.last_name as employee_last_name', 'employees.image as employee_image')
                ->where(function ($query) {
                    $query->where('documents.employee_id', auth()->user()->employee_id); // User's own documents
                    
                })
                ->orderBy('documents.created_at', 'desc')
                ->get();
        }

        $employees = Employee::all();

        return view('admin.pages.document.index', compact('documents','employees'));
    }





    public function showByEmployee($employeeId)
    {
       
        // Retrieve all documents for the specified employee
        $documents = Document::where('documents.employee_id', $employeeId)
            
            ->leftJoin('employees', 'documents.employee_id', '=', 'employees.id')
            ->select(
                'documents.*',
                'employees.first_name as employee_first_name',
                'employees.last_name as employee_last_name',
                'employees.image as employee_image'
            )
            ->get();

        // Check if no documents are found
        $noData = $documents->isEmpty();
       
        $employees = Employee::all();
        // Return the view with the documents and the noData flag
        return view('admin.pages.document.index', compact('documents', 'noData','employees'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $employees = Employee::all();
    $id = null;
    $first_name = null;
    $last_name = null;

    return view('admin.pages.document.create', compact('employees', 'id', 'first_name', 'last_name'));
}


    public function creates($title = null, $id = null,$first_name = null, $last_name = null)
    {
      

        $employees = Employee::all();

        return view('admin.pages.document.create', compact('employees', 'title', 'id','first_name','last_name'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'name' => 'required|string|max:255',
        'document' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:2048',
        'employee_id' => 'nullable|exists:employees,id' // Validate employee_id if provided
    ]);

    // Create a new Document instance
    $document = new Document();
    $document->name = $request->input('name');

    // Check if the user is an admin, HR, or Accountant
    if (auth()->user()->role == 'admin' || auth()->user()->role == 'HR' || auth()->user()->role == 'Accountant') {
        // If employee_id is provided, assign it to the document
        $document->employee_id = $request->input('employee_id');
    } else {
        // Otherwise, use the logged-in user's employee_id
        $document->employee_id = auth()->user()->employee_id;
    }

    // Handle the file upload
    if ($request->hasFile('document')) {
        $file = $request->file('document');
        $filename = time() . '_' . $file->getClientOriginalName(); // Use the original file name with a timestamp
        $file->move(public_path('images/documents'), $filename); // Move the file to the 'images/documents' directory
        $document->document = 'images/documents/' . $filename; // Store the file path in the database
    }

    // Save the document record to the database
    $document->save();

    // Update the status in the accouncement_documents table
    if ($request->input('id')) {
        DB::table('accouncement_documents')
            ->where('employee_id', $request->input('id'))
            ->update(['status' => 1]);
    }

    // Redirect back with a success message
    return redirect()->route('document.index')->with('success', 'Document uploaded successfully!');
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
        
        // Retrieve the document by ID
        $document = Document::find($id);

        // Only load the employees if the logged-in user is an admin, HR, or Accountant
        $employees = [];
        if (auth()->user()->role == 'admin' || auth()->user()->role == 'HR' || auth()->user()->role == 'Accountant') {
            $employees = Employee::all(); // Get all employees
        }

        // Return the view with document and employees (if applicable)
        return view('admin.pages.document.edit', compact('document', 'employees'));
    }


    public function edits($title = null, $id = null,$first_name = null, $last_name = null,$docid = null)
    {
        $document = Document::find($docid);
      
        $employees = [];
        if (auth()->user()->role == 'admin' || auth()->user()->role == 'HR' || auth()->user()->role == 'Accountant') {
            $employees = Employee::all(); // Get all employees
        }

        // Return the view with document and employees (if applicable)
        return view('admin.pages.document.edit', compact('document','employees','title','id','first_name','last_name'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
       
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'document' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:2048', // Document is optional
            'employee_id' => 'nullable|exists:employees,id' // Validate employee_id if provided
        ]);

        // Find the existing document record
        $document = Document::findOrFail($id);

        // Update the name
        $document->name = $request->input('name');

        // Check if the user is an admin, HR, or Accountant
        if (auth()->user()->role == 'admin' || auth()->user()->role == 'HR' || auth()->user()->role == 'Accountant') {
            // If employee_id is provided, assign it to the document
            $document->employee_id = $request->input('employee_id');
        } else {
            // Otherwise, use the logged-in user's employee_id
            $document->employee_id = auth()->user()->employee_id;
        }

        // Handle the file upload if a new file is provided
        if ($request->hasFile('document')) {
            // Delete the old file if it exists
            if (file_exists(public_path($document->document))) {
                unlink(public_path($document->document));
            }

            // Upload the new file
            $file = $request->file('document');
            $filename = time() . '_' . $file->getClientOriginalName(); // Use the original file extension
            $file->move(public_path('images/documents'), $filename); // Move the file to the correct directory
            $document->document = 'images/documents/' . $filename; // Update the path in the database
        }

        // Save the updated document record to the database
        $document->save();

        if ($id) {
            DB::table('documents')
                ->where('id', $id)
                ->update(['status' => 0]);
        }

    

        // Redirect back with a success message
        return redirect()->route('document.index')->with('success', 'Document updated successfully!');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $document = Document::findOrFail($id);
        $document->delete();
        return redirect()->route('document.index')->with('success', 'Document Deleted successfully!');
    }


    public function updateStatus(Request $request, $id)
{
    $document = Document::findOrFail($id); // Find the document

    $request->validate([
        'status' => 'required|in:0,1,2', // Validate the status
    ]);

    $document->status = $request->status; // Update the status
    $document->save(); // Save the changes

    return response()->json(['status' => $document->status]);
}

}
