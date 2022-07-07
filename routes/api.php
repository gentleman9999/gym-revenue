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

Route::prefix('leads')->group(function () {
    Route::get('/', \App\Actions\Endusers\Leads\ReadLeads::class);
    Route::post('/create', \App\Actions\Endusers\Leads\CreateLeadApi::class);
    Route::post('/upsert', \App\Actions\Endusers\Leads\UpsertLeadApi::class);
    Route::post('/batchupsert', \App\Actions\Endusers\Leads\BatchUpsertLeadApi::class);
});

Route::prefix('members')->group(function () {
    Route::get('/', \App\Actions\Endusers\Members\ReadMembers::class);
    Route::post('/create', \App\Actions\Endusers\Members\CreateMemberApi::class);
    Route::post('/upsert', \App\Actions\Endusers\Members\UpsertMemberApi::class);
    Route::post('/batchupsert', \App\Actions\Endusers\Members\BatchUpsertMemberApi::class);
});

Route::post('/plans', \App\Actions\Clients\GetPlans::class);
