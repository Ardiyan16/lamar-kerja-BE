<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataController;
use App\Http\Middleware\isApi;
use App\Http\Middleware\IsApiAdmin;
use App\Http\Middleware\isApiCompany;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-company', [AuthController::class, 'register_company']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgot_password']);
Route::post('/reset-password', [AuthController::class, 'reset_password']);
Route::post('/verification-account', [AuthController::class, 'verification_account']);
Route::get('/login-google', [AuthController::class, 'login_google']);
Route::get('/login-google/callback', [AuthController::class, 'login_google_callback']);

Route::prefix('select')->group(function() {
    Route::get('/province', [DataController::class, 'select_province']);
    Route::get('/regency/{id}', [DataController::class, 'select_regency']);
    Route::get('/district/{id}', [DataController::class, 'select_district']);
    Route::get('/village/{id}', [DataController::class, 'select_village']);
});

Route::prefix('employee')->middleware(isApi::class)->group(function() {
    Route::get('/check-me', [AuthController::class, 'check_me']);
    Route::get('/user', [AuthController::class, 'user']);
});

Route::prefix('company')->middleware(isApiCompany::class)->group(function() {
    Route::get('/', [AuthController::class, 'user']);
    Route::get('/check-auth', [AuthController::class, 'check_auth']);
    Route::get('/type-industry', [DataController::class, 'data_type_industry']);
    Route::prefix('profile')->group(function() {
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/update', [CompanyController::class, 'update']);
        Route::post('/upload-profile', [CompanyController::class, 'upload_profile']);
        Route::post('/delete-image-profile', [CompanyController::class, 'delete_image_profile']);
        Route::post('/upload-gallery', [CompanyController::class, 'upload_gallery']);
    });

});

Route::prefix('admin')->middleware(IsApiAdmin::class)->group(function() {
    Route::get('/', [AuthController::class, 'user']);
    Route::get('/check-auth', [AuthController::class, 'check_auth']);
    Route::get('/get-dashboard', [DashboardController::class, 'admin']);

    //type industry
    Route::prefix('type-industry')->group(function() {
        Route::get('/', [DataController::class, 'data_type_industry']);
        Route::post('/save', [DataController::class, 'save_type_industry']);
        Route::get('/delete/{id}', [DataController::class, 'delete_type_industry']);
    });

    //field works
    Route::prefix('field-work')->group(function() {
        Route::get('/', [DataController::class, 'data_field_work']);
        Route::post('/save', [DataController::class, 'save_field_work']);
        Route::get('/delete/{id}', [DataController::class, 'delete_field_work']);
    });

    //sub field works
    Route::prefix('sub-field-work')->group(function() {
        Route::get('/', [DataController::class, 'data_sub_field_work']);
        Route::post('/save', [DataController::class, 'save_sub_field_work']);
        Route::get('/delete/{id}', [DataController::class, 'delete_sub_field_work']);
    });
});