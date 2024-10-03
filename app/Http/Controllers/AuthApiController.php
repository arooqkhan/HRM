<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    // public function login(Request $request)
    // {
    //     // Validate the request input
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);
    
    //     // Retrieve the user by email
    //     $user = User::where('email', $request->email)->first();
    
    //     // Check if user exists and if password matches
    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         // Return custom error message in the response
    //         return response()->json([
    //             'message' => 'Invalid username or password.',
    //             'status' => '401'
    //         ], 401); 
    //     }
    
    //     // Generate a token for authenticated user
    //     $token = $user->createToken('auth_token')->plainTextToken;
    
    //     // Return success response with token
    //     return response()->json([
    //         'message' => 'Login successful',
    //         'access_token' => $token,
    //         'data' =>$user
    //     ]);
    // }

    public function login(Request $request)
    {
        // Validate the request input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Retrieve the user by email
        $user = User::where('email', $request->email)->first();
    
        // Check if user exists and if password matches
        if (!$user || !Hash::check($request->password, $user->password)) {
            // Return custom error message in the response
            return response()->json([
                'message' => 'Invalid username or password.',
                'status' => '401'
            ], 401);
        }
    
        // Generate a token for authenticated user
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // Construct the full URL for the user's image
        $imagePath = $user->image ? $user->image : 'default_image.png';
        $user->image_url = url($imagePath);
        unset($user->image);
    
        // Return success response with token and user data
        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'data' => $user
        ]);
    }

}
