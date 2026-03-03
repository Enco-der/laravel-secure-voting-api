<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Assuming you are fetching from users table

class VoterController extends Controller
{
    public function index()
    {
        $users = User::all(); // fetch all users

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
}
