<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;


Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
});




// protected route
Route::middleware('auth:sanctum')->group(function () {


    // categories
    Route::apiResource('categories',CategoryController::class);



    // user logout
    Route::post('/auth/logout', [AuthController::class, 'logout']);


});


