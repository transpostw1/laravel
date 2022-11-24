<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\RatesController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PortsController;
//use App\Http\Controllers\SendEmailController;

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
    Route::post('rates/pdf', 'pdf');

});

Route::controller(BookingsController::class)->group(function () {
    Route::get('bookings', 'index');
    Route::get('bookings/select/{id}', 'show');
    Route::get('bookings/user/', 'user');
    Route::post('bookings/store/', 'store');
    Route::get('bookings/timeline/', 'timeline');

});

Route::controller(CustomerController::class)->group(function () {
    Route::post('customer/store/', 'store');

});

//Route::get('/search', [Select2SearchController::class, 'index']);
// Route::get('/ajax-autocomplete-search', [PortsController::class, 'selectSearch']);
Route::controller(PortsController::class)->group(function () {
    Route::get('ajax-autocomplete-search', 'selectSearch');

});
// Route::middleware('auth:api')->group(function () {
// 	// JWT protected routes
// });


//Route::get('send-email', [SendEmailController::class, 'index']);
