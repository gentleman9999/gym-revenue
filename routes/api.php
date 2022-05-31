<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/leads', \App\Actions\Endusers\Leads\ReadLeads::class);


/*
Route::middleware('auth:sanctum')->prefix('/members')->group(function () {
    Route::get('/', \App\Actions\Endusers\Members\ReadMembers::class)->name('members.read');
});*/
