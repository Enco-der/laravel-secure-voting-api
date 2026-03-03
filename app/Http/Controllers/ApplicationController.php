<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        // Validate form data
        $request->validate([
            'applicant_name' => 'required|string|max:255',
            'email'          => 'required|email|max:255',
            'country'        => 'required|string|max:255',
            'organization'   => 'required|string|max:255',
            'applied_date'   => 'required|date',
            'region_code'    => 'required|string|max:50',
            'access_scope'   => 'required|string|max:50',
            'feedback'       => 'nullable|string',
            'documents.*'    => 'file|mimes:pdf,jpg,png,doc,docx|max:2048' // max 2MB
        ]);

        // Handle file uploads
        $documentPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $filename = Str::random(10) . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('public/documents', $filename);
                $documentPaths[] = $path;
            }
        }

        // Save to database
        $application = Application::create([
            'applicant_name' => $request->applicant_name,
            'email'          => $request->email,
            'country'        => $request->country,
            'organization'   => $request->organization,
            'applied_date'   => $request->applied_date,
            'region_code'    => $request->region_code,
            'access_scope'   => $request->access_scope,
            'feedback'       => $request->feedback,
            'status'         => 'Pending',
            'documents'      => $documentPaths
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully!',
            'data'    => $application
        ]);
    }
}
