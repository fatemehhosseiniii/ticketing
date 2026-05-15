<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Profile\NotificationController;
use App\Http\Controllers\Api\Tickets\TicketApprovalController;
use App\Http\Controllers\Api\Tickets\TicketController;
use App\Http\Controllers\Api\Tickets\TicketRejectionController;
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


    //profile Routes
    Route::prefix('profile')->group(function () {
       Route::get('notifications', NotificationController::class);
    });
});




//fake endpoint
Route::post('endpoint/fake/confirm-ticket', FakeEndpointController::class);
