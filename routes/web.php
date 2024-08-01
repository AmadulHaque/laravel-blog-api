<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    abort(404);
});

Route::get('/login',function (){
    return errorResponse('Unauthorized',[],401);
})->name('login');
