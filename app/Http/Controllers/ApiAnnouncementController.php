<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class ApiAnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve all announcements with employee details
        $announcements = Announcement::with('employee')->get();

        // Check if there are announcements
        if ($announcements->isEmpty()) {
            return response()->json([
                'message' => 'No announcements found.',
                'announcements' => []
            ], 404);
        }

        // Map announcements to include employee names
        $announcementsWithEmployeeNames = $announcements->map(function ($announcement) {
            return [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'message' => $announcement->message,
                'date' => $announcement->date,
                'employee_name' => $announcement->employee 
                    ? $announcement->employee->first_name . ' ' . $announcement->employee->last_name 
                    : 'Unknown',
            ];
        });

        // Return a success response with the announcements
        return response()->json([
            'announcements' => $announcementsWithEmployeeNames
        ], 200);
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
        
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'date' => 'required|date',
        ]);

        $employeeId = auth()->user()->employee_id;
       
        
    
        if (!$employeeId) {
            return response()->json([
                'error' => 'No employee record associated with this user.'
            ], 400);
        }

        $announcement = new Announcement();
        $announcement->title = $validatedData['title'];
        $announcement->message = $validatedData['message'];
        $announcement->date = $validatedData['date'];
        $announcement->employee_id = $employeeId;
        $announcement->save();

       return response()->json([
        'message' =>'Announcement created successfully.',
        'status' => true
       ],200);
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
    public function destroy($id)
    {
        // Find the announcement by ID
        $announcement = Announcement::find($id);

        // Check if the announcement exists
        if (!$announcement) {
            return response()->json([
                'error' => 'Announcement not found.'
            ], 404);
        }

        // Delete the announcement
        $announcement->delete();

        // Return a success response
        return response()->json([
            'success' => 'Announcement deleted successfully.',
            'status' => true
        ], 200);
    }
}
