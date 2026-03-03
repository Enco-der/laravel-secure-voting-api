<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    // Register voter by default

public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'cnic' => 'required|digits:13|unique:users,cnic',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:3',
        'cnic_image' => 'nullable|file|mimes:jpg,jpeg,png|max:5120', // 5MB
        'live_image' => 'nullable|string' // will receive Base64
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    // CNIC image
    $cnicPath = null;
    if ($request->hasFile('cnic_image')) {
        $cnicPath = $request->file('cnic_image')->store('cnic_images', 'public');
    }

    // Live image (Base64)
    $livePath = null;
    if ($request->live_image) {
        $image_parts = explode(";base64,", $request->live_image);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1]; // jpg, png
        $image_base64 = base64_decode($image_parts[1]);

        $fileName = uniqid() . '.' . $image_type;

        // Ensure directory exists
        $liveDir = storage_path('app/public/live_image/');
        if (!file_exists($liveDir)) {
            mkdir($liveDir, 0755, true);
        }

        // Save file
        file_put_contents($liveDir . $fileName, $image_base64);

        $livePath = 'live_image/' . $fileName;
    }

    $user = User::create([
        'name' => $request->name,
        'cnic' => $request->cnic,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => 'voter',
        'voter_key' => Str::uuid(),
        'has_voted' => false,
        'cnic_image' => $cnicPath,
        'live_image' => $livePath,
    ]);

    $token = $user->createToken('api_token')->plainTextToken;

    return response()->json([
        'message' => 'User created successfully',
        'token' => $token,
        'cnic_path' => $cnicPath,
        'live_path' => $livePath
    ], 201);
}




                                                        // Login
   public function login(Request $request)
{
    // Validate
    $validateuser = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
        'cnic' => 'required|digits:13'

    ]);

    if ($validateuser->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Authentication error',
            'error' => $validateuser->errors()->all()
        ], 401);
    }

    // Find user
    $user = User::where('email', $request->email)->where('cnic', $request->cnic)->first();


    // If user not found OR password wrong
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Email or password incorrect'
        ], 401);
    }

    // Login Success
    return response()->json([
        'status' => true,
        'message' => 'User Logged in Successfully',
        'token' => $user->createToken('anything')->plainTextToken,
        'token_type' => 'bearer'
    ], 200);
}

    

    // Get current user
    public function user(Request $request)
{
    $user = $request->user(); // get authenticated user

    // Return only necessary info
    return response()->json([
        'name' => $user->name,
        'email' => $user->email,
        'voter_key' => $user->voter_key,
        'has_voted' => $user->has_voted,
    ]);
}






   public function logout(Request $request)
{
    $request->user()->tokens()->delete();
    return response()->json(['message' => 'Logged out']);
}

}

