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
    return redirect('login');
    /*
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
    */
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
Route::middleware(['auth:sanctum', 'verified'])->delete('/locations/{id}', \App\Http\Controllers\LocationsController::class.'@trash')->name('locations.trash');
Route::middleware(['auth:sanctum', 'verified'])->post('/locations/{id}/restore', \App\Http\Controllers\LocationsController::class.'@restore')->name('locations.restore');

Route::middleware(['auth:sanctum', 'verified'])->prefix('comms')->group( function() {
    Route::get('/', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@index')->name('comms.dashboard');
    Route::get('/email-templates', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@et_index')->name('comms.email-templates');
    Route::get('/email-templates/create', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@et_create')->name('comms.email-templates.create');
    Route::get('/email-templates/{id}', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@et_edit')->name('comms.email-templates.edit');
    Route::post('/email-templates', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@et_store')->name('comms.email-templates.store');
    Route::put('/email-templates/{id}', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@et_update')->name('comms.email-templates.update');

    Route::get('/email-campaigns', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@ec_index')->name('comms.email-campaigns');
    Route::get('/email-campaigns/create', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@ec_create')->name('comms.email-campaigns.create');
    Route::get('/email-campaigns/{id}', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@ec_edit')->name('comms.email-campaigns.edit');
    Route::post('/email-campaigns', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@ec_create')->name('comms.email-campaigns.create');
    Route::put('/email-campaigns/{id}', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@ec_update')->name('comms.email-campaigns.update');

    Route::get('/sms-campaigns', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@sc_index')->name('comms.sms-campaigns');
    Route::get('/sms-campaigns/create', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@sc_create')->name('comms.sms-campaigns.create');
    Route::get('/sms-campaigns/{id}', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@sc_edit')->name('comms.sms-campaigns.edit');
    Route::post('/sms-campaigns', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@sc_store')->name('comms.sms-campaigns.store');
    Route::put('/sms-campaigns/{id}', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@sc_update')->name('comms.sms-campaigns.update');

    Route::get('/sms-templates', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@st_index')->name('comms.sms-templates');
    Route::get('/sms-templates/create', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@st_create')->name('comms.sms-templates.create');
    Route::get('/sms-templates/{id}', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@st_edit')->name('comms.sms-templates.edit');
    Route::post('/sms-templates', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@st_store')->name('comms.sms-templates.store');
    Route::put('/sms-templates/{id}', \App\Http\Controllers\Comm\MassCommunicationsController::class.'@st_update')->name('comms.sms-templates.update');
});
Route::middleware(['auth:sanctum', 'verified'])->prefix('data')->group( function() {
    Route::prefix('leads')->group( function() {
        Route::get('/', \App\Http\Controllers\Data\LeadsController::class.'@index')->name('data.leads');
        Route::get('/create', \App\Http\Controllers\Data\LeadsController::class.'@create')->name('data.leads.create');
        Route::post('/create', \App\Http\Controllers\Data\LeadsController::class.'@store')->name('data.leads.store');
        Route::get('/show/{id}', \App\Http\Controllers\Data\LeadsController::class.'@show')->name('data.leads.show');
        Route::get('/edit/{id}', \App\Http\Controllers\Data\LeadsController::class.'@edit')->name('data.leads.edit');
        Route::put('/{id}', \App\Http\Controllers\Data\LeadsController::class.'@update')->name('data.leads.update');
        Route::post('/assign', \App\Http\Controllers\Data\LeadsController::class.'@assign')->name('data.leads.assign');
        Route::post('/contact/{id}', \App\Http\Controllers\Data\LeadsController::class.'@contact')->name('data.leads.contact');
    });

    Route::get('/conversions', \App\Http\Controllers\DashboardController::class.'@index')->name('data.conversions');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('files')->group( function() {
    Route::get('/', \App\Http\Controllers\FilesController::class.'@index')->name('files');
    Route::get('/upload', \App\Http\Controllers\FilesController::class.'@upload')->name('files.upload');
    Route::post('/', \App\Http\Controllers\FilesController::class.'@store')->name('files.store');
    Route::get('/edit/{id}', \App\Http\Controllers\FilesController::class.'@edit')->name('files.edit');
    Route::put('/{id}', \App\Http\Controllers\FilesController::class.'@update')->name('files.update');
    Route::delete('/{id}', \App\Http\Controllers\FilesController::class.'@trash')->name('files.trash');
    Route::delete('/{id}/force', \App\Http\Controllers\FilesController::class.'@delete')->name('files.delete');
    Route::post('/{id}/restore', \App\Http\Controllers\FilesController::class.'@restore')->name('files.restore');


});
