<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\isApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-google', [AuthController::class, 'register_google']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/login-company', [AuthController::class, 'login_company']);
Route::post('/forgot-password', [AuthController::class, 'forgot_password']);
Route::post('/verification-account', [AuthController::class, 'verification_account']);

Route::prefix('employee')->middleware(isApi::class)->group(function() {
    Route::get('/check-me', [AuthController::class, 'check_me']);
    Route::get('/user', [AuthController::class, 'user']);
});