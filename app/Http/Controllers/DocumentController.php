<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       
        // Retrieve documents based on user role
        if (auth()->user()->role == 'admin' || auth()->user()->role =='HR' || auth()->user()->role =='Accountant') {
            // Admin retrieves all documents with associated employee details
            $documents = Document::leftJoin('employees', 'documents.employee_id', '=', 'employees.id')
                ->select('documents.*', 'employees.first_name as employee_first_name', 'employees.last_name as employee_last_name', 'employees.image as employee_image')
                ->get();
        } else {
            // Non-admins (employees) should see:
            // - Their own documents, or
            // - Documents with status 1
            $documents = Document::leftJoin('employees', 'documents.employee_id', '=', 'employees.id')
                ->select('documents.*', 'employees.first_name as employee_first_name', 'employees.last_name as employee_last_name', 'employees.image as employee_image')
                ->where(function($query) {
                    $query->where('documents.employee_id', auth()->user()->employee_id) // User's own documents
                          ->Where('documents.status', 1); // Documents with status 1
                })
                ->get();
        }
    
        return view('admin.pages.document.index', compact('documents'));
    }
    
    
    
    

public function showByEmployee($employeeId)
{
    // Retrieve all documents for the specified employee
    $documents = Document::where('documents.employee_id', $employeeId) // Specify the table name
        ->leftJoin('employees', 'documents.employee_id', '=', 'employees.id')
        ->select('documents.*', 'employees.first_name as employee_first_name', 'employees.last_name as employee_last_name', 'employees.image as employee_image')
        ->get();

    // Check if no documents are found
    $noData = $documents->isEmpty();

    // Return the view with the documents and the noData flag
    return view('admin.pages.document.index', compact('documents', 'noData'));
}

    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        return view('admin.pages.document.create');
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
        ]);
    
        // Create a new Document instance
        $document = new Document();
        $document->name = $request->input('name');
        $document->employee_id = Auth::user()->employee_id; 
    
        // Handle the file upload
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $filename = time() . '_' . $file->getClientOriginalName(); // Use the original file extension
            $file->move(public_path('images/documents'), $filename); // Move the file to the correct directory
            $document->document = 'images/documents/' . $filename; // Store the path in the database
        }
    
        // Save the document record to the database
        $document->save();
    
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
       
        $document = Document::find($id);
        return view('admin.pages.document.edit',compact('document'));
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
    ]);

    // Find the existing document record
    $document = Document::findOrFail($id);

    // Update the name
    $document->name = $request->input('name');
    $document->employee_id = Auth::user()->employee_id; 

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


    public function updateStatus(Request $request)
{
    $document = Document::findOrFail($request->id);
    
    // Update the document's status
    $document->status = $request->status;
    $document->save();

    return response()->json(['success' => true]);
}




}