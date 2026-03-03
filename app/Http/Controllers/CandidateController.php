<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
class CandidateController extends Controller
{
    //
    public function store(Request $request)
{
    $request->validate([
        'username' => 'required|unique:candidates',
        'password' => 'required|min:5',
        'applicant_name' => 'required',
        'email' => 'required|email|unique:candidates',
        'country' => 'required',
        'organization' => 'required',
        'applied_date' => 'required|date',
        'region_code' => 'required',
        'access_scope' => 'required',
        'party_ticket' => 'required',
        'cnic_picture' => 'required|mimes:jpg,jpeg,png',
        'documents.*' => 'mimes:jpg,jpeg,png,pdf'
    ]);

    // Upload CNIC picture
    $cnicPath = $request->file('cnic_picture')->store('cnic_pictures', 'public');

    // Upload multiple documents
    $documentPaths = [];
    if ($request->hasFile('documents')) {
        foreach ($request->file('documents') as $file) {
            $documentPaths[] = $file->store('documents', 'public');
        }
    }

    Candidate::create([
        'username' => $request->username,
        'password' => bcrypt($request->password),
        'applicant_name' => $request->applicant_name,
        'email' => $request->email,
        'country' => $request->country,
        'organization' => $request->organization,
        'applied_date' => $request->applied_date,
        'region_code' => $request->region_code,
        'access_scope' => $request->access_scope,
        'party_ticket' => $request->party_ticket,
        'cnic_picture' => $cnicPath,
                'documents' => $documentPaths,
        'feedback' => $request->feedback
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Candidate registered successfully!'
    ]);
}

}
