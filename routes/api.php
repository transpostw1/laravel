<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\RatesController;
use App\Http\Controllers\BookingsController;

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});

Route::controller(TodoController::class)->group(function () {
    Route::get('todos', 'index');
    Route::post('todo', 'store');
    Route::get('todo/{id}', 'show');
    Route::put('todo/{id}', 'update');
    Route::delete('todo/{id}', 'destroy');
}); 

Route::controller(RatesController::class)->group(function () {
    Route::get('rates', 'index');
    Route::get('rates/select', 'select');
    Route::get('rates/liverates', 'liverates');
    Route::get('rates/cma_rates', 'cma_rates');
    
}); 

Route::controller(BookingsController::class)->group(function () {
    Route::get('bookings', 'index');
    Route::get('bookings/select/{id}', 'show');
    Route::get('bookings/user/', 'user');
    
}); 

Route::middleware('auth:api')->group(function () {
	// JWT protected routes
});
