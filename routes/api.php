<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('guest')->group(function () {


    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', RegisterController::class);

});


Route::prefix('dashboard')->middleware('auth:api')->group(function () {

    //tickets List
    Route::resource('tickets',TicketController::class)->only('index','show', 'store','destroy');
});
