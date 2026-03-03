<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuth;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\VoterController;
use App\Http\Controllers\OtpVerification;  
use App\Http\Controllers\SuperAdminAuthController;
use App\Http\Controllers\SuperAdminController;  
use App\Http\Controllers\CandidateController;
use App\Models\Admin;

// ------------------------------------------------------------------------------------
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// -------------------------------------------------------------------------------------

Route::post('/send-otp', [OtpVerification::class, 'sendOtp']);
Route::post('/verify-otp', [OtpVerification::class, 'verifyOtp']);

// -------------------------------------------------------------------------------------
                               //loged in user details


Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);


// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);  
});





// super admin tasks
Route::post('/create-admin', [AdminAuth::class, 'createAdmin']);
Route::get('/showadmins', [AdminAuth::class, 'showAdmins']); 
Route::get('/admins/{id}', [AdminAuth::class, 'updateAdmin']);   
Route::delete('/admins/{id}', [AdminAuth::class, 'deleteAdmin']);
Route::put('/superadmin/update', [SuperAdminController::class, 'updateSuperAdmin']);
Route::get('/users', [VoterController::class, 'index']);     // to see all the voters by admin


Route::post('/superadmin/login', [SuperAdminAuthController::class, 'login']); 
Route::post('/admin/login', [AdminAuth::class, 'AdminLogin']);




// admin tasks



    
Route::get('/voters', [AdminAuth::class, 'getAllVoters']);





// candidate 

Route::post('/candidate', [CandidateController::class, 'store']);
