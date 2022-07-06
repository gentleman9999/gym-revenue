<?php

use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', \App\Http\Controllers\DashboardController::class . '@index')->name('dashboard');
Route::middleware(['auth:sanctum', 'verified'])->get('/analytics', \App\Http\Controllers\DashboardController::class . '@index')->name('analytics');
Route::middleware(['auth:sanctum', 'verified'])->get('/workout-generator', \App\Http\Controllers\WorkoutGeneratorController::class . '@index')->name('workout-generator');
Route::middleware(['auth:sanctum', 'verified'])->get('/sales-slideshow', \App\Http\Controllers\WorkoutGeneratorController::class . '@index')->name('sales-slideshow');
Route::middleware(['auth:sanctum', 'verified'])->get('/payment-gateways', \App\Http\Controllers\WorkoutGeneratorController::class . '@index')->name('payment-gateways');

Route::middleware(['auth:sanctum', 'verified'])->put('/current-location', \App\Http\Controllers\LocationsController::class . '@switch')->name('current-location.update');
Route::middleware(['auth:sanctum', 'verified'])->put('/current-team', \App\Domain\Users\Actions\SwitchTeam::class)->name('current-team.update');
//@todo: need to add in ACL/middleware for CnB users
Route::middleware(['auth:sanctum', 'verified'])->prefix('locations')->group(function () {
    Route::get('/', \App\Http\Controllers\LocationsController::class . '@index')->name('locations');
    Route::get('/create', \App\Http\Controllers\LocationsController::class . '@create')->name('locations.create');
    Route::get('/export', \App\Http\Controllers\LocationsController::class . '@export')->name('locations.export');
    Route::get('/{id}', \App\Http\Controllers\LocationsController::class . '@edit')->name('locations.edit');
    Route::get('/view/{id}', \App\Http\Controllers\LocationsController::class . '@view')->name('locations.view');
    Route::post('/', \App\Actions\Clients\Locations\CreateLocation::class)->name('locations.store');
    Route::post('/import', \App\Actions\Clients\Locations\ImportLocations::class)->name('locations.import');
    Route::put('/{id}', \App\Actions\Clients\Locations\UpdateLocation::class)->name('locations.update')->where(['id' => '[0-9]+']);
    Route::delete('/{id}', \App\Actions\Clients\Locations\TrashLocation::class)->name('locations.trash')->where(['id' => '[0-9]+']);
    Route::post('/{id}/restore', \App\Actions\Clients\Locations\RestoreLocation::class)->name('locations.restore')->where(['id' => '[0-9]+']);
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('user')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\UserProfileController::class, 'show'])->name('profile.show');
    Route::post('/tokens', \App\Domain\Users\Actions\GrantAccessToken::class)->name('api-tokens.store');
});
Route::middleware(['auth:sanctum', 'verified'])->prefix('comms')->group(function () {
    Route::get('/', \App\Http\Controllers\Comm\MassCommunicationsController::class . '@index')->name('comms.dashboard');
    Route::get('/export', \App\Http\Controllers\Comm\MassCommunicationsController::class . '@export')->name('comms.export');

    Route::middleware(['auth:sanctum', 'verified'])->prefix('scheduled-campaigns')->group(function () {
        Route::get('', \App\Http\Controllers\Comm\ScheduledCampaignsController::class . '@index')->name('comms.scheduled-campaigns');
        Route::get('/create', \App\Http\Controllers\Comm\ScheduledCampaignsController::class . '@create')->name('comms.scheduled-campaigns.create');
        Route::get('/export', \App\Http\Controllers\Comm\ScheduledCampaignsController::class . '@export')->name('comms.scheduled-campaigns.export');
        Route::get('/{scheduledCampaign}', \App\Http\Controllers\Comm\ScheduledCampaignsController::class . '@edit')->name('comms.scheduled-campaigns.edit');
        Route::post('/', \App\Domain\Campaigns\ScheduledCampaigns\Actions\CreateScheduledCampaign::class)->name('comms.scheduled-campaigns.store');
        Route::put('/{scheduledCampaign}', \App\Domain\Campaigns\ScheduledCampaigns\Actions\UpdateScheduledCampaign::class)->name('comms.scheduled-campaigns.update');
        Route::delete('/{scheduledCampaign}', \App\Domain\Campaigns\ScheduledCampaigns\Actions\TrashScheduledCampaign::class)->name('comms.scheduled-campaigns.trash');
        Route::post('/{scheduledCampaign}/restore', \App\Domain\Campaigns\ScheduledCampaigns\Actions\RestoreScheduledCampaign::class)->withTrashed()->name('comms.scheduled-campaigns.restore');
    });

    Route::middleware(['auth:sanctum', 'verified'])->prefix('drip-campaigns')->group(function () {
        Route::get('', \App\Http\Controllers\Comm\DripCampaignsController::class . '@index')->name('comms.drip-campaigns');
        Route::get('/create', \App\Http\Controllers\Comm\DripCampaignsController::class . '@create')->name('comms.drip-campaigns.create');
        Route::get('/export', \App\Http\Controllers\Comm\DripCampaignsController::class . '@export')->name('comms.drip-campaigns.export');
        Route::get('/{dripCampaign}', \App\Http\Controllers\Comm\DripCampaignsController::class . '@edit')->name('comms.drip-campaigns.edit');
        Route::post('/', \App\Domain\Campaigns\DripCampaigns\Actions\CreateDripCampaign::class)->name('comms.drip-campaigns.store');
        Route::put('/{dripCampaign}', \App\Domain\Campaigns\DripCampaigns\Actions\UpdateDripCampaign::class)->name('comms.drip-campaigns.update');
        Route::delete('/{dripCampaign}', \App\Domain\Campaigns\DripCampaigns\Actions\TrashDripCampaign::class)->name('comms.drip-campaigns.trash');
        Route::post('/{dripCampaign}/restore', \App\Domain\Campaigns\DripCampaigns\Actions\RestoreDripCampaign::class)->withTrashed()->name('comms.drip-campaigns.restore');
    });

    Route::middleware(['auth:sanctum', 'verified'])->prefix('sms-templates')->group(function () {
        Route::get('', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@index')->name('comms.sms-templates');
        Route::get('/create', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@create')->name('comms.sms-templates.create');
        Route::get('/export', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@export')->name('comms.sms-templates.export');
        Route::get('/{id}', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@edit')->name('comms.sms-templates.edit');
        Route::post('/', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@store')->name('comms.sms-templates.store');
        Route::put('/{id}', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@update')->name('comms.sms-templates.update');
        Route::delete('/{id}', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@trash')->name('comms.sms-templates.trash');
        Route::post('/{id}/restore', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@restore')->name('comms.sms-templates.restore');
        Route::post('/test', \App\Actions\Sms\SendATestText::class)->name('comms.sms-templates.test-msg');
    });
    Route::middleware(['auth:sanctum', 'verified'])->prefix('email-templates')->group(function () {
        Route::get('/', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@index')->name('comms.email-templates');
        Route::get('/create', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@create')->name('comms.email-templates.create');
        Route::get('/export', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@export')->name('comms.email-templates.export');
        Route::get('/{id}', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@edit')->name('comms.email-templates.edit');
        Route::post('/', \App\Actions\Clients\Activity\Comms\CreateEmailTemplate::class)->name('comms.email-templates.store');
        Route::put('/{id}', \App\Actions\Clients\Activity\Comms\UpdateEmailTemplate::class)->name('comms.email-templates.update');
        Route::delete('/{id}', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@trash')->name('comms.email-templates.trash');
        Route::post('/{id}/restore', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@restore')->name('comms.email-templates.restore');
        Route::post('/test', \App\Actions\Mail\SendATestEmail::class)->name('comms.email-templates.test-msg');
    });
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('data')->group(function () {
    Route::prefix('leads')->group(function () {
        Route::get('/', \App\Http\Controllers\Data\LeadsController::class . '@index')->name('data.leads');
        Route::get('/claimed', \App\Http\Controllers\Data\LeadsController::class . '@claimed')->name('data.leads.claimed');
        Route::get('/create', \App\Http\Controllers\Data\LeadsController::class . '@create')->name('data.leads.create');
        Route::post('/create', \App\Actions\Endusers\Leads\CreateLead::class)->name('data.leads.store');
        Route::get('/show/{id}', \App\Http\Controllers\Data\LeadsController::class . '@show')->name('data.leads.show');
        Route::get('/edit/{id}', \App\Http\Controllers\Data\LeadsController::class . '@edit')->name('data.leads.edit');
        Route::put('/{id}', \App\Actions\Endusers\Leads\UpdateLead::class)->name('data.leads.update');
        Route::post('/assign', \App\Http\Controllers\Data\LeadsController::class . '@assign')->name('data.leads.assign');
        Route::post('/contact/{id}', \App\Http\Controllers\Data\LeadsController::class . '@contact')->name('data.leads.contact');
        Route::get('/sources', \App\Http\Controllers\Data\LeadsController::class . '@sources')->name('data.leads.sources');
        Route::post('/sources/update', \App\Http\Controllers\Data\LeadsController::class . '@updateSources')->name('data.leads.sources.update');
        Route::get('/statuses', \App\Http\Controllers\Data\LeadsController::class . '@statuses')->name('data.leads.statuses');
        Route::post('/statuses/update', \App\Http\Controllers\Data\LeadsController::class . '@updateStatuses')->name('data.leads.statuses.update');
        Route::delete('/delete/{id}', \App\Actions\Endusers\Leads\TrashLead::class)->name('data.leads.trash');
        Route::post('/delete/{id}/restore', \App\Actions\Endusers\Leads\RestoreLead::class)->name('data.leads.restore');
        Route::get('/view/{id}', \App\Http\Controllers\Data\LeadsController::class . '@view')->name('data.leads.view');
        Route::get('/export', \App\Http\Controllers\Data\LeadsController::class . '@export')->name('data.leads.export');
    });

    Route::prefix('members')->group(function () {
        Route::get('/', \App\Http\Controllers\Data\MembersController::class . '@index')->name('data.members');
        Route::get('/create', \App\Http\Controllers\Data\MembersController::class . '@create')->name('data.members.create');
        Route::post('/', \App\Actions\Endusers\Members\CreateMember::class)->name('data.members.store');
        Route::get('/show/{id}', \App\Http\Controllers\Data\MembersController::class . '@show')->name('data.members.show');
        Route::get('/edit/{id}', \App\Http\Controllers\Data\MembersController::class . '@edit')->name('data.members.edit');
        Route::put('/{id}', \App\Actions\Endusers\Members\UpdateMember::class)->name('data.members.update');
        Route::post('/contact/{id}', \App\Http\Controllers\Data\MembersController::class . '@contact')->name('data.members.contact');
        Route::delete('/delete/{id}', \App\Actions\Endusers\Members\TrashMember::class)->name('data.members.trash');
        Route::post('/delete/{id}/restore', \App\Actions\Endusers\MEmbers\RestoreMember::class)->name('data.members.restore');
        Route::get('/view/{id}', \App\Http\Controllers\Data\MembersController::class . '@view')->name('data.members.view');
        Route::get('/export', \App\Http\Controllers\Data\MembersController::class . '@export')->name('data.members.export');
    });

    Route::get('/conversions', \App\Http\Controllers\DashboardController::class . '@index')->name('data.conversions');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('files')->group(function () {
    Route::get('/', \App\Http\Controllers\FilesController::class . '@index')->name('files');
    Route::get('/upload', \App\Http\Controllers\FilesController::class . '@upload')->name('files.upload');
    Route::post('/', \App\Actions\Clients\Files\CreateFiles::class)->name('files.store');
    Route::get('/edit/{id}', \App\Http\Controllers\FilesController::class . '@edit')->name('files.edit');
    Route::put('/{id}/rename', \App\Actions\Clients\Files\RenameFile::class)->name('files.rename');
    Route::put('/{id}', \App\Actions\Clients\Files\UpdateFile::class)->name('files.update');
    Route::delete('/{id}', \App\Actions\Clients\Files\TrashFile::class)->name('files.trash');
    Route::delete('/{id}/force', \App\Actions\Clients\Files\DeleteFile::class)->name('files.delete');
    Route::post('/{id}/restore', \App\Actions\Clients\Files\RestoreFile::class)->name('files.restore');
    Route::get('/export', \App\Http\Controllers\FilesController::class . '@export')->name('files.export');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('calendar')->group(function () {
    Route::get('/', \App\Http\Controllers\CalendarController::class . '@index')->name('calendar');
    Route::post('/', \App\Actions\Clients\Calendar\CreateCalendarEvent::class)->name('calendar.event.store');
    Route::put('/{id}', \App\Actions\Clients\Calendar\UpdateCalendarEvent::class)->name('calendar.event.update');
    Route::delete('/reminder/delete/{id}', \App\Domain\Reminders\Actions\DeleteReminder::class)->name('calendar.reminder.delete');
    Route::put('/reminder/create/{id}', \App\Domain\Reminders\Actions\CreateReminderFromCalendarEvent::class)->name('calendar.reminder.create');
    Route::put('/complete_task/{id}', \App\Actions\Clients\Tasks\MarkTaskComplete::class)->name('calendar.complete_event');
    Route::post('/upload', \App\Actions\Clients\Calendar\UploadFile::class)->name('calendar.upload');
    Route::prefix('event_types')->group(function () {
        Route::get('/', \App\Http\Controllers\CalendarController::class . '@eventTypes')->name('calendar.event_types');
        Route::get('/create', \App\Http\Controllers\CalendarController::class . '@createEventType')->name('calendar.event_types.create');
        Route::post('/', \App\Actions\Clients\Calendar\CalendarEventTypes\CreateCalendarEventType::class)->name('calendar.event_types.store');
        Route::get('/edit/{id}', \App\Http\Controllers\CalendarController::class . '@editEventType')->name('calendar.event_types.edit');
        Route::put('/{id}', \App\Actions\Clients\Calendar\CalendarEventTypes\UpdateCalendarEventType::class)->name('calendar.event_types.update');
        Route::delete('/{id}', \App\Actions\Clients\Calendar\CalendarEventTypes\TrashCalendarEventType::class)->name('calendar.event_types.trash');
        Route::delete('/{id}/force', \App\Actions\Clients\Calendar\CalendarEventTypes\DeleteCalendarEventType::class)->name('calendar.event_types.delete');
        Route::post('/{id}/restore', \App\Actions\Clients\Calendar\CalendarEventTypes\RestoreCalendarEventType::class)->name('calendar.event_types.restore');
    });
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('users')->group(function () {
    Route::get('/', \App\Http\Controllers\UsersController::class . '@index')->name('users');
    Route::get('/create', \App\Http\Controllers\UsersController::class . '@create')->name('users.create');
    Route::post('/', \App\Domain\Users\Actions\CreateUser::class)->name('users.store');
    Route::post('/import', \App\Domain\Users\Actions\ImportUsers::class)->name('users.import');
    Route::get('/edit/{user}', \App\Http\Controllers\UsersController::class . '@edit')->name('users.edit');
    Route::get('/view/{user}', \App\Http\Controllers\UsersController::class . '@view')->name('users.view');
    Route::put('/{user}', \App\Domain\Users\Actions\UpdateUser::class)->name('users.update');
    Route::delete('/{user}', \App\Domain\Users\Actions\DeleteUser::class)->name('users.delete');
    Route::post('/{user}/documents', \App\Actions\Teams\UploadDocForUser::class . '@upload')->name('users.documents.create');
    Route::get('/export', \App\Http\Controllers\UsersController::class . '@export')->name('users.export');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('teams')->group(function () {
    Route::get('/', \App\Http\Controllers\TeamController::class . '@index')->name('teams');
    Route::get('/create', \App\Http\Controllers\TeamController::class . '@create')->name('teams.create');
    Route::post('/', \App\Domain\Teams\Actions\CreateTeam::class)->name('teams.store');
    Route::get('/edit/{id}', \App\Http\Controllers\TeamController::class . '@edit')->name('teams.edit');
    Route::get('/view/{id}', \App\Http\Controllers\TeamController::class . '@view')->name('teams.view');
//    for some reason, the commented route below gets overridden by the default teams route
    Route::post('/{team}/members', \App\Domain\Teams\Actions\AddOrInviteTeamMembers::class)->name('team-member.store');
    Route::delete('/{team}/{teamMemberId}', \App\Domain\Teams\Actions\RemoveTeamMember::class)->name('team-members.destroy');
    Route::put('/update/{team}', \App\Domain\Teams\Actions\UpdateTeam::class)->name('team.update');
    Route::delete('/{team}', \App\Domain\Teams\Actions\DeleteTeam::class)->name('teams.delete');
    Route::get('/export', \App\Http\Controllers\TeamController::class . '@export')->name('teams.export');
});
Route::middleware(['auth:sanctum', 'verified'])->prefix('settings')->group(function () {
    Route::get('/', \App\Http\Controllers\ClientSettingsController::class . '@index')->name('settings');
    Route::post('/client-services', \App\Domain\Clients\Actions\SetClientServices::class)->name('settings.client-services.update');
    Route::post('/trial-memberships', \App\Http\Controllers\ClientSettingsController::class . '@updateTrialMembershipTypes')->name('settings.trial-membership-types.update');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('roles')->group(function () {
    Route::get('/', \App\Http\Controllers\RolesController::class . '@index')->name('roles');
    Route::get('/create', \App\Http\Controllers\RolesController::class . '@create')->name('roles.create');
    Route::post('/', \App\Actions\Clients\Roles\CreateRole::class)->name('roles.store');
    Route::get('/edit/{id}', \App\Http\Controllers\RolesController::class . '@edit')->name('roles.edit');
    Route::put('/{id}', \App\Actions\Clients\Roles\UpdateRole::class)->name('roles.update');
    Route::delete('/{id}', \App\Actions\Clients\Roles\TrashRole::class)->name('roles.trash');
    Route::delete('/{id}/force', \App\Actions\Clients\Roles\DeleteRole::class)->name('roles.delete');
    Route::post('/{id}/restore', \App\Actions\Clients\Roles\RestoreRole::class)->name('roles.restore');
    Route::get('/export', \App\Http\Controllers\RolesController::class . '@export')->name('roles.export');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('classifications')->group(function () {
    Route::get('/', \App\Http\Controllers\ClassificationsController::class . '@index')->name('classifications');
    Route::get('/create', \App\Http\Controllers\ClassificationsController::class . '@create')->name('classifications.create');
    Route::post('/', \App\Actions\Clients\Classifications\CreateClassification::class)->name('classifications.store');
    Route::get('/edit/{id}', \App\Http\Controllers\ClassificationsController::class . '@edit')->name('classifications.edit');
    Route::put('/{id}', \App\Actions\Clients\Classifications\UpdateClassification::class)->name('classifications.update');
    Route::delete('/{id}', \App\Actions\Clients\Classifications\TrashClassification::class)->name('classifications.trash');
    Route::delete('/{id}/force', \App\Actions\Clients\Classifications\DeleteClassification::class)->name('classifications.delete');
    Route::post('/{id}/restore', \App\Actions\Clients\Classifications\RestoreClassification::class)->name('classifications.restore');
    Route::get('/export', \App\Http\Controllers\ClassificationsController::class . '@export')->name('classifications.export');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('tasks')->group(function () {
    Route::get('/', \App\Http\Controllers\TaskController::class . '@index')->name('tasks');
    Route::delete('/{id}', \App\Actions\Clients\Calendar\DeleteCalendarEvent::class)->name('tasks.delete');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('impersonation')->group(function () {
    Route::post('/users', \App\Domain\Users\Actions\GetUsersToImpersonate::class)->name('impersonation.users');
});
Route::prefix('impersonation')->group(function () {
    Route::post('/on', \App\Domain\Users\Actions\ImpersonateUser::class)->name('impersonation.start');
    Route::post('/off', \App\Domain\Users\Actions\StopImpersonatingUser::class)->name('impersonation.stop');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('note')->group(function () {
    Route::post('/', \App\Actions\Notes\MarkNoteAsRead::class)->name('note.seen');
});
Route::middleware(['auth:sanctum', 'verified'])->prefix('crud')->group(function () {
    Route::post('/', \App\Domain\Users\Actions\SetCustomUserCrudColumns::class)->name('crud-customize');
});

Route::prefix('invite')->group(function () {
    Route::get('/{id}', \App\Http\Controllers\InviteController::class . '@index')->name('invite');
    Route::post('/accept', \App\Actions\Clients\Calendar\AcceptInvite::class)->name('invite.accept');
    Route::post('/decline', \App\Actions\Clients\Calendar\DeclineInvite::class)->name('invite.decline');
});
Route::middleware(['auth:sanctum', 'verified'])->prefix('notifications')->group(function () {
    Route::get('/', \App\Domain\Notifications\Actions\GetNotifications::class)->name('notifications');
    Route::get('/unread', \App\Domain\Notifications\Actions\GetUnreadNotificationCount::class)->name('notifications.unread');
    Route::post('/{id}', \App\Domain\Notifications\Actions\DismissNotification::class)->name('notifications.dismiss');
});
Route::middleware(['auth:sanctum', 'verified'])->prefix('reports')->group(function () {
    Route::get('/', \App\Http\Controllers\ReportsDashboardController::class . '@index')->name('reports.dashboard');
    Route::get('/{type}', \App\Http\Controllers\ReportsDashboardController::class . '@page')->name('reports.page');
});

Route::prefix('s')->group(function () {
    Route::get('/{id}', \App\Http\Controllers\ShortUrlController::class . '@index')->name('short');
});

//since this is for endusers to opt in/out of communications, we don't want to load up the CRM App bundle
Route::prefix('/communication-preferences')->group(function () {
    Route::get('/l/{lead}', \App\Http\Controllers\Data\LeadsController::class . '@leadCommunicationPreferences')->name('comms-prefs.lead');
    Route::post('/l/{lead}', \App\Http\Controllers\Data\LeadsController::class . '@updateLeadCommunicationPreferences')->name('comms-prefs.lead.update');
    Route::get('/m/{member}', \App\Http\Controllers\Data\MembersController::class . '@memberCommunicationPreferences')->name('comms-prefs.member');
    Route::post('/m/{member}', \App\Http\Controllers\Data\MembersController::class . '@updateMemberCommunicationPreferences')->name('comms-prefs.member.update');
});

//TODO: this is the new mass-comm dash - will replace current one in near future.
Route::middleware(['auth:sanctum', 'verified'])->prefix('mass-com')->group(function () {
    Route::get('/', \App\Http\Controllers\MassCommunicationController::class . '@index')->name('mass_com.dashboard');
    Route::get('/{type}', \App\Http\Controllers\MassCommunicationController::class . '@page')->name('mass_com.page');
});
