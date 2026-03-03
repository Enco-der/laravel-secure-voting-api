<?php

namespace App\Http\Controllers;
use App\Models\EmailOtp;
use Illuminate\Support\Facades\Mail;


use Illuminate\Http\Request;

class OtpVerification extends Controller
{
    
    public function sendOtp(Request $request){
    $request->validate([
        'email' => 'required|email'
    ]);

    $email = $request->email;

    // Generate 6-digit OTP
    $otp = rand(100000, 999999);

    // Store in DB (update if exists)
    EmailOtp::updateOrCreate(
        ['email' => $email],
        [
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5)
        ]
    );

    // Send Email
    Mail::raw("Your OTP is: $otp", function ($message) use ($email) {
        $message->to($email)->subject("Your Verification OTP");
    });

    return response()->json([
        'message' => 'OTP sent to your email.'
    ]);
}





// -----------------------------------------------------------------------------------------------
public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required'
    ]);

    $record = EmailOtp::where('email', $request->email)->first();

    if (!$record) {
        return response()->json(['error' => 'OTP not found'], 404);
    }

    if ($record->otp !== $request->otp) {
        return response()->json(['error' => 'Invalid OTP'], 400);
    }

    if (now()->greaterThan($record->expires_at)) {
        return response()->json(['error' => 'OTP expired'], 400);
    }

    return response()->json(['message' => 'OTP verified successfully']);
}




// -----------------------------------------------------------------------------------------

}
