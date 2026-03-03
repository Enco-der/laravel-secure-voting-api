<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Validator;

class SuperAdminController extends Controller
{
    public function updateSuperAdmin(Request $request)
    {
        // Since we have only one Super Admin, get the first one
        $superAdmin = SuperAdmin::first();
        if (!$superAdmin) {
            return response()->json([
                'message' => 'Super Admin not found'
            ], 404);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'username'  => 'sometimes|string|max:255',
            'email'     => 'sometimes|email|unique:super_admins,email,' . $superAdmin->id,
            'password'  => 'sometimes|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Update fields
        if ($request->has('username')) {
            $superAdmin->username = $request->username;
        }

        if ($request->has('email')) {
            $superAdmin->email = $request->email;
        }

        if ($request->has('password')) {
            $superAdmin->password = bcrypt($request->password);
        }

        $superAdmin->save();

        return response()->json([
            'message' => 'Super Admin updated successfully',
            'superAdmin' => $superAdmin
        ], 200);
    }
}
