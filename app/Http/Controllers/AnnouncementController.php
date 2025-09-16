<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function __construct()
    {
        $this->middleware('permission:view announcements', ['only' => ['index']]);
        $this->middleware('permission:create announcements', ['only' => ['create','store']]);
        $this->middleware('permission:update announcements', ['only' => ['update','edit']]);
        $this->middleware('permission:delete announcements', ['only' => ['destroy']]);
        $this->middleware('permission:show announcements', ['only' => ['show']]);
    }

    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->get();
        return view('admin.pages.announcement.index',compact('announcements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $announcements = Announcement::all();
        return view('admin.pages.announcement.create',compact('announcements'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'date' => 'required|date',
        ]);
    
        $user = auth()->user();
       
        $employeeId = $user->id;
    
        if (!$employeeId) {
            return redirect()->back()->with('error', 'No employee record associated with this user.');
        }
    
        $announcement = new Announcement();
        $announcement->title = $validatedData['title'];
        $announcement->message = $validatedData['message'];
        $announcement->date = $validatedData['date'];
        $announcement->employee_id = $employeeId;
        $announcement->save();
    
        return redirect()->route('announcements.index')->with('success', 'Announcement created successfully.');
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

       $announcement = Announcement::findOrFail($id);
        return view('admin.pages.announcement.edit', compact('announcement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
  
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'message' => 'required|string',
        'date' => 'required|date',
    ]);

   
    $announcement = Announcement::findOrFail($id);

    $announcement->title = $validatedData['title'];
    $announcement->message = $validatedData['message'];
    $announcement->date = $validatedData['date'];
    $announcement->employee_id = auth()->user()->id; 

    $announcement->save();
    return redirect()->route('announcements.index')->with('success', 'Announcement updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    
    $announcement = Announcement::findOrFail($id);
    $announcement->delete();
    return redirect()->route('announcements.index')->with('success', 'Announcement deleted successfully.');
}

}
