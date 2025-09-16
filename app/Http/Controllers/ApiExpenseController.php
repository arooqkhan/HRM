<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ApiExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve all expense records
        $expenses = Expense::all();

        // Modify the image path to include the full URL
        $expenses->transform(function ($expense) {
            $expense->image = URL::to('/') . '/' . $expense->image;
            return $expense;
        });

        // Return a JSON response with the list of expenses
        return response()->json([
            'success' => true,
            'data'=> $expenses
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
        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'date' => 'required|date_format:Y-m-d',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Optional, validate image files
        ]);

        // Handle validation failure
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create a new Expense record
        $expense = new Expense();
        $expense->name = $request->name;
        $expense->price = $request->price;
        $expense->date = $request->date;

        // Handle custom file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_image.' . $image->getClientOriginalExtension(); // Custom image name
            $image->move(public_path('images'), $imageName); // Move the image to the public/images directory
            $imagePath = 'images/' . $imageName; // Set the path to store in the database
            $expense->image = $imagePath;
        }

        $expense->save();

        return response()->json([
            'success' => true,
            'message' => 'Expense created successfully',
           
        ], 200);
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
    public function update(Request $request, $id)
    {
        // Find the expense by ID
        $expense = Expense::find($id);

        // Check if the expense exists
        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found'
            ], 404);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'date' => 'sometimes|required|date',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // If validation fails, return an error response
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        // Update the expense data
        $expense->name = $request->input('name', $expense->name);
        $expense->price = $request->input('price', $expense->price);
        $expense->date = $request->input('date', $expense->date);

        // If an image is provided, update the image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_image.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $expense->image = 'images/' . $imageName;
        }

        // Save the updated expense
        $expense->save();

        // Modify the image path to include the full URL
        $expense->image = URL::to('/') . '/' . $expense->image;

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'data' => $expense
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy($id)
    {
        // Find the expense by ID
        $expense = Expense::find($id);

        // Check if the expense exists
        if (!$expense) {
            return response()->json([
                'success' => false,
                'message' => 'Expense not found'
            ], 404);
        }

        // Delete the expense
        $expense->delete();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully'
        ], 200);
    }
}
