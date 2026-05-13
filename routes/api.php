<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('guest')->group(function () {


    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', RegisterController::class);

});
