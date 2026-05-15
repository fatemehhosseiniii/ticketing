<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\TicketApprovalController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketRejectionController;
use App\Http\Controllers\FakeEndpointController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->middleware('guest')->group(function () {


    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', RegisterController::class);

});


Route::prefix('dashboard')->middleware('auth:api')->group(function () {

    //tickets List
    Route::resource('tickets', TicketController::class)->only('index', 'show', 'store', 'destroy');

    //management ticket
    Route::prefix('tickets/{ticket}')->group(function () {

        //reject
        Route::patch('/rejected', TicketRejectionController::class);
        //approval routes
        Route::patch('/accepted', TicketApprovalController::class);
    });

});




//fake endpoint
Route::post('endpoint/fake/confirm-ticket', FakeEndpointController::class);
