<?php

use App\Http\Controllers\Authenticate\ResetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authenticate\AuthenticationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function(){
         
    Route::post('/register',[AuthenticationController::class,'register'])->name('register');
    Route::post('/login',[AuthenticationController::class,'login'])->name('login');

    Route::post('/reset_password_request',[ResetPasswordController::class,'reset_password_request'])->name('reset_password_request');
});