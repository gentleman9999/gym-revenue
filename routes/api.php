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

Route::middleware('bearer')->prefix('leads')->group(function () {
    Route::get('/', \App\Domain\EndUsers\Leads\Actions\ReadLeads::class);
    Route::post('/create', \App\Domain\EndUsers\Leads\Actions\CreateLeadApi::class);
    Route::post('/upsert', \App\Domain\EndUsers\Leads\Actions\UpsertLeadApi::class);
    Route::post('/batchupsert', \App\Domain\EndUsers\Leads\Actions\BatchUpsertLeadApi::class);
});

Route::middleware('bearer')->prefix('members')->group(function () {
    Route::get('/', \App\Domain\EndUsers\Members\Actions\ReadMembers::class);
    Route::post('/create', \App\Domain\EndUsers\Members\Actions\CreateMemberApi::class);
    Route::post('/upsert', \App\Domain\EndUsers\Members\Actions\UpsertMemberApi::class);
    Route::post('/batchupsert', \App\Domain\EndUsers\Members\Actions\BatchUpsertMemberApi::class);
});

Route::middleware('twilio')->prefix('twilio')->group(function () {
    Route::post('/statusCallBack', \App\Domain\SMS\Actions\StatusCallback::class);
});

Route::post('/plans', \App\Actions\Clients\GetPlans::class);
