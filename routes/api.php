<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::prefix('oauth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
});




// protected route
Route::middleware('auth:api')->group(function () {

    // user logout
    Route::post('/oauth/logout', [AuthController::class, 'logout']);


});


