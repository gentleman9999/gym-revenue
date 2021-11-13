<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', \App\Http\Controllers\DashboardController::class.'@index')->name('dashboard');
Route::middleware(['auth:sanctum', 'verified'])->get('/analytics', \App\Http\Controllers\DashboardController::class.'@index')->name('analytics');
Route::middleware(['auth:sanctum', 'verified'])->get('/workout-generator', \App\Http\Controllers\WorkoutGeneratorController::class.'@index')->name('workout-generator');
Route::middleware(['auth:sanctum', 'verified'])->get('/sales-slideshow', \App\Http\Controllers\WorkoutGeneratorController::class.'@index')->name('sales-slideshow');
Route::middleware(['auth:sanctum', 'verified'])->get('/payment-gateways', \App\Http\Controllers\WorkoutGeneratorController::class.'@index')->name('payment-gateways');

Route::middleware(['auth:sanctum', 'verified'])->put('/current-location', \App\Http\Controllers\LocationsController::class.'@switch')->name('current-location.update');
//@todo: need to add in ACL/middleware for CnB users
Route::middleware(['auth:sanctum', 'verified'])->get('/locations', \App\Http\Controllers\LocationsController::class.'@index')->name('locations');
Route::middleware(['auth:sanctum', 'verified'])->get('/locations/create', \App\Http\Controllers\LocationsController::class.'@create')->name('locations.create');
Route::middleware(['auth:sanctum', 'verified'])->get('/locations/{id}', \App\Http\Controllers\LocationsController::class.'@edit')->name('locations.edit');
Route::middleware(['auth:sanctum', 'verified'])->post('/locations', \App\Http\Controllers\LocationsController::class.'@store')->name('locations.store');
Route::middleware(['auth:sanctum', 'verified'])->put('/locations/{id}', \App\Http\Controllers\LocationsController::class.'@update')->name('locations.update');
Route::middleware(['auth:sanctum', 'verified'])->delete('/locations/{id}', \App\Http\Controllers\LocationsController::class.'@delete')->name('locations.delete');
Route::middleware(['auth:sanctum', 'verified'])->post('/locations/{id}/restore', \App\Http\Controllers\LocationsController::class.'@restore')->name('locations.restore');

Route::middleware(['auth:sanctum', 'verified'])->prefix('data')->group( function() {
    Route::prefix('leads')->group( function() {
        Route::get('/', \App\Http\Controllers\Data\LeadsController::class.'@index')->name('data.leads');
        Route::get('/create', \App\Http\Controllers\Data\LeadsController::class.'@create')->name('data.leads.create');
        Route::post('/create', \App\Http\Controllers\Data\LeadsController::class.'@store')->name('data.leads.store');
        Route::get('/show/{id}', \App\Http\Controllers\Data\LeadsController::class.'@show')->name('data.leads.show');
        Route::get('/edit/{id}', \App\Http\Controllers\Data\LeadsController::class.'@edit')->name('data.leads.edit');
        Route::put('/{id}', \App\Http\Controllers\Data\LeadsController::class.'@update')->name('data.leads.update');
        Route::post('/assign', \App\Http\Controllers\Data\LeadsController::class.'@assign')->name('data.leads.assign');
    });

    Route::get('/conversions', \App\Http\Controllers\DashboardController::class.'@index')->name('data.conversions');
});
