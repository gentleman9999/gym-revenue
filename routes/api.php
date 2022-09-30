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

Route::middleware('auth:sanctum')->group(static function () {
    Route::get('/user', static fn (Request $request) => $request->user());
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

Route::prefix('twilio')->group(static function () {
    Route::middleware('TwilioAuth')->group(static function () {
        Route::post('/statusCallBack', \App\Domain\SMS\Actions\TwilioStatusCallback::class);
        Route::post('conversation/{client_id}', \App\Domain\Conversations\Twilio\Actions\JoinConversation::class);

        Route::prefix('call')->group(static function () {
            Route::post('connect/{phone}', \App\Domain\VoiceCalls\Actions\ConnectPhone::class);
            Route::post('status/{user}/provider/{gateway}', \App\Domain\VoiceCalls\Actions\UpdateStatus::class);
        });
    });
});

Route::middleware('MailgunAuth')->prefix('mailgun')->group(function () {
    Route::post('/statusCallBack', \App\Domain\Email\Actions\MailgunStatusCallback::class);
});


Route::post('/plans', \App\Actions\Clients\GetPlans::class);
