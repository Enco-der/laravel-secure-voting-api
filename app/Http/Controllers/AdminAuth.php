<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Mail\AdminCredentialsMail;
use Illuminate\Support\Facades\Mail;

class AdminAuth extends Controller
{

   public function AdminLogin(Request $request)
{
    // Validate
    $validateuser = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
        

    ]);

    if ($validateuser->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Authentication error',
            'error' => $validateuser->errors()->all()
        ], 401);
    }

    // Find user
    $admin = Admin::where('email', $request->email)->first();


    // If user not found OR password wrong
    if (!$admin || !Hash::check($request->password, $admin->password)) {
        return response()->json([
            'status' => false,
            'message' => 'Email or password incorrect'
        ], 401);
    }

    // Login Success
    return response()->json([
        'status' => true,
        'message' => 'User Logged in Successfully',
        'token' => $admin->createToken('anything')->plainTextToken,
        'token_type' => 'bearer'
    ], 200);
}



    public function createAdmin(Request $request)  {
        $validateuser = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
        ]);

        if ($validateuser->fails()) {
            return response("Validation failed: " . implode(", ", $validateuser->errors()->all()), 422);
        }

        $plainPassword = $request->password;

        $admin = Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($plainPassword),
            'role'     => 'admin',
        ]);

        // send credentials email
        Mail::to($admin->email)->send(new AdminCredentialsMail($admin->email, $plainPassword));

        return "✅ Admin created and credentials emailed!";
    }


// --------------------------------------------------------------------------------------------------
      public function showadmins()
    {
        // Fetch only username and email
         $admins = Admin::all();
        return response()->json([
            'success' => true,
            'data' => $admins
        ], 200);
    }


// ---------------------------------------------------------------------------------------------------

public function updateAdmin(Request $request, $id)
    {
        // Find admin by ID
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'message' => 'Admin not found'
            ], 404);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'name'  => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:admins,email,' . $id,
            'password' => 'sometimes|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Update fields
        if ($request->has('name')) {
            $admin->name = $request->name;
        }

        if ($request->has('email')) {
            $admin->email = $request->email;
        }

        if ($request->has('password')) {
            $admin->password = bcrypt($request->password);
        }

        $admin->save();

        return response()->json([
            'message' => 'Admin updated successfully',
            'admin'   => $admin
        ], 200);
    }

    // -------------------------------------------------------------------------------------------------------------------------
    public function deleteAdmin($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'message' => 'Admin not found'
            ], 404);
        }

        $admin->delete();

        return response()->json([
            'message' => 'Admin deleted successfully'
        ], 200);
    }




    // -------------------------------------------------------------------------------------

    // managing voters 
      public function getAllVoters(Request $request)
    {
        $voters = User::all(['name', 'email', 'password', 'role', 'voter_key', 'has_voted']);

        return response()->json([
            'status' => true,
            'voters' => $voters
        ]);
    }
}
