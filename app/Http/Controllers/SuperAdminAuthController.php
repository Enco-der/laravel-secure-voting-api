<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SuperAdminAuthController extends Controller
{
   public function login(Request $request)
{
    // Validate input
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|min:3',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Find super admin by email
    $admin = SuperAdmin::where('email', $request->email)->first();

    // Plain text password check
    if (!$admin || $request->password !== $admin->password) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    // Return success response
    return response()->json([
        'status' => true,
        'message' => 'Login successful',
        'data' => [
            'id' => $admin->id,
            'email' => $admin->email,
            'name' => $admin->name,
        ]
    ]);
}

}
