<?php

namespace App\Http\Controllers;

use App\Mail\EmployeeDocument;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\AccouncementDocument;
use Illuminate\Support\Facades\Mail;

class AccouncementDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function __construct()
     {
         $this->middleware('permission:view announcementsdocument', ['only' => ['index']]);
         $this->middleware('permission:create announcementsdocument', ['only' => ['create','store']]);
         $this->middleware('permission:update announcementsdocument', ['only' => ['update','edit']]);
         $this->middleware('permission:delete announcementsdocument', ['only' => ['destroy']]);
         $this->middleware('permission:show announcementsdocument', ['only' => ['show']]);
     }


     public function index()
     {
         // Get the currently authenticated user
         $user = auth()->user();
     
         // Check the user's role
         if ($user->role === 'admin' || $user->role === 'HR' || $user->role === 'Accountant') {
             // Show all records if the user is an admin, HR, or Accountant, ordered by latest first
             $accouncementdocuments = AccouncementDocument::with('employee')
                 ->orderBy('created_at', 'desc')
                 ->get();
         } else {
             // Show only the records that belong to the currently authenticated user, ordered by latest first
             $accouncementdocuments = AccouncementDocument::with('employee')
                 ->where('employee_id', $user->employee_id) // Assuming `employee_id` is the field storing the user's ID
                 ->orderBy('created_at', 'desc')
                 ->get();
         }
     
         $employees = Employee::all();
     
         // Return the view with the data
         return view('admin.pages.accouncementdocument.index', compact('accouncementdocuments','employees'));
     }
     

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::all();
        return view('admin.pages.accouncementdocument.create',compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $user = auth()->user();
   
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'title' => 'required|string|max:255',
    ]);

    // Create a new AccouncementDocument
    $accouncementDocument = new AccouncementDocument();
    $accouncementDocument->employee_id = $request->employee_id;
    $accouncementDocument->title = $request->title;
   

    // Save the document
    $accouncementDocument->save();

    Mail::to($user->employee->contact_email)->send(new EmployeeDocument($accouncementDocument));

    // Redirect with a success message
    return redirect()->route('accouncementdocument.index')->with('success', 'Announcement document created successfully.');
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
    // Find the Accouncement Document by its ID
    $accouncementdocument = AccouncementDocument::find($id);

    // Check if the document exists
    if (!$accouncementdocument) {
        return redirect()->route('accouncementdocument.index')->with('error', 'Accouncement Document not found.');
    }

    // Attempt to delete the document
    $accouncementdocument->delete();

    // Redirect back with a success message
    return redirect()->route('accouncementdocument.index')->with('success', 'Accouncement Document deleted successfully.');
}


public function updateStatus(Request $request, $id)
{
    // Find the announcement document by ID
    $announcementDocument = AccouncementDocument::findOrFail($id);

    // Update the status to 1
    $announcementDocument->status = 1;
    $announcementDocument->save();

    // Return a success message
    return redirect()->back()->with('success', 'Ok I Will Uploading');
}






}
