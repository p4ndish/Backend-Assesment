<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\EmailVerificationController;

// Registration and email verification routes
Route::post('/auth/register', [RegisterController::class, 'register']);
Route::post('/auth/verify-email', [EmailVerificationController::class, 'verify']);
Route::get('/auth/verify-email', [EmailVerificationController::class, 'verify']); // GET route for direct link clicks
Route::post('/auth/resend-verification', [RegisterController::class, 'resendVerification']);
Route::post('/auth/check-verification', [EmailVerificationController::class, 'checkVerification']);

// Public routes - no authentication required
Route::post('/auth/login', [AuthController::class, 'login'])->name('login');
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPasswordGet'])->middleware('throttle:3,1');
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:3,1');
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:3,1');
Route::post('/auth/resend-otp', [AuthController::class, 'resendOTP'])->middleware('throttle:3,1');

// Protected routes - authentication required
Route::middleware('auth:api')->group(function () {
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:3,1');
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/getUser', [AuthController::class, 'me']);
    Route::get('/auth/getUserImage', [StorageController::class, 'getUserImage']);
    Route::post('/auth/saveUserImage', [StorageController::class, 'storeUserImage']);
    Route::post('/auth/update-user', [AuthController::class, 'updateUser']);
    Route::post('/auth/update-password', [AuthController::class, 'updatePassword']);
});
