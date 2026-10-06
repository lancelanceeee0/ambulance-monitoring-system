<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\AmbulanceController;

// ---- your existing auth routes (unchanged) ----
Route::get('/', [UserController::class, 'showLogin'])->name('login');

Route::post('/login', [UserController::class, 'login']);

Route::get('/register', [UserController::class, 'showRegister']);

Route::post('/register', [UserController::class, 'register']);

Route::post('/logout', [UserController::class, 'logout']);

// ---- dispatch system (login required) ----
Route::middleware('auth')->group(function () {
    Route::get('/dispatch', [DispatchController::class, 'index']);
    Route::post('/dispatch', [DispatchController::class, 'record']);
    Route::get('/dispatch-log', [DispatchController::class, 'logs']);
    Route::get('/ambulance-arrival', [DispatchController::class, 'arrival']);
    Route::post('/ambulance-arrival/{log}/complete', [DispatchController::class, 'completeArrival']);

    Route::get('/vehicle-registration', [AmbulanceController::class, 'create']);
    Route::post('/vehicle-registration', [AmbulanceController::class, 'store']);
});
