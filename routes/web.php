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
    Route::post('/tokens', \App\Actions\Fortify\GrantAccessToken::class)->name('api-tokens.store');
});
Route::middleware(['auth:sanctum', 'verified'])->prefix('comms')->group(function () {
    Route::get('/', \App\Http\Controllers\Comm\MassCommunicationsController::class . '@index')->name('comms.dashboard');
    Route::get('/export', \App\Http\Controllers\Comm\MassCommunicationsController::class . '@export')->name('comms.export');

    Route::middleware(['auth:sanctum', 'verified'])->prefix('email-campaigns')->group(function () {
        Route::get('', \App\Http\Controllers\Comm\EmailCampaignsController::class . '@index')->name('comms.email-campaigns');
        Route::get('/create', \App\Http\Controllers\Comm\EmailCampaignsController::class . '@create')->name('comms.email-campaigns.create');
        Route::get('/export', \App\Http\Controllers\Comm\EmailCampaignsController::class . '@export')->name('comms.email-campaigns.export');
        Route::get('/{id}', \App\Http\Controllers\Comm\EmailCampaignsController::class . '@edit')->name('comms.email-campaigns.edit');
        Route::post('/', \App\Http\Controllers\Comm\EmailCampaignsController::class . '@store')->name('comms.email-campaigns.store');
        Route::put('/{id}', \App\Http\Controllers\Comm\EmailCampaignsController::class . '@update')->name('comms.email-campaigns.update');
        Route::delete('/{id}', \App\Http\Controllers\Comm\EmailCampaignsController::class . '@trash')->name('comms.email-campaigns.trash');
        Route::post('/{id}/restore', \App\Http\Controllers\Comm\EmailCampaignsController::class . '@restore')->name('email.email-campaigns.restore');
    });

    Route::middleware(['auth:sanctum', 'verified'])->prefix('sms-campaigns')->group(function () {
        Route::get('', \App\Http\Controllers\Comm\SmsCampaignsController::class . '@index')->name('comms.sms-campaigns');
        Route::get('/create', \App\Http\Controllers\Comm\SmsCampaignsController::class . '@create')->name('comms.sms-campaigns.create');
        Route::get('/export', \App\Http\Controllers\Comm\SmsCampaignsController::class . '@export')->name('comms.sms-campaigns.export');
        Route::get('/{id}', \App\Http\Controllers\Comm\SmsCampaignsController::class . '@edit')->name('comms.sms-campaigns.edit');
        Route::post('/', \App\Http\Controllers\Comm\SmsCampaignsController::class . '@store')->name('comms.sms-campaigns.store');
        Route::put('/{id}', \App\Http\Controllers\Comm\SmsCampaignsController::class . '@update')->name('comms.sms-campaigns.update');
        Route::delete('/{id}', \App\Http\Controllers\Comm\SmsCampaignsController::class . '@trash')->name('comms.sms-campaigns.trash');
        Route::post('/{id}/restore', \App\Http\Controllers\Comm\SmsCampaignsController::class . '@restore')->name('comms.sms-campaigns.restore');
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
    Route::get('/quickview', \App\Http\Controllers\CalendarController::class . '@quickView')->name('calendar.quickview');
    Route::post('/', \App\Actions\Clients\Calendar\CreateCalendarEvent::class)->name('calendar.event.store');
    Route::put('/{id}', \App\Actions\Clients\Calendar\UpdateCalendarEvent::class)->name('calendar.event.update');
    Route::delete('/reminder/delete/{id}', \App\Actions\Users\Reminders\DeleteReminder::class)->name('calendar.reminder.delete');
    Route::put('/reminder/create/{id}', \App\Actions\Users\Reminders\CreateReminderFromCalendarEvent::class)->name('calendar.reminder.create');
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
    Route::post('/', \App\Actions\Fortify\CreateUser::class)->name('users.store');
    Route::post('/import', \App\Actions\Fortify\ImportUsers::class)->name('users.import');
    Route::get('/edit/{id}', \App\Http\Controllers\UsersController::class . '@edit')->name('users.edit')->where(['id' => '[0-9]+']);
    Route::get('/view/{id}', \App\Http\Controllers\UsersController::class . '@view')->name('users.view')->where(['id' => '[0-9]+']);
    Route::put('/{id}', \App\Actions\Fortify\UpdateUser::class)->name('users.update')->where(['id' => '[0-9]+']);
    Route::delete('/{id}', \App\Actions\Jetstream\DeleteUser::class)->name('users.delete')->where(['id' => '[0-9]+']);
    Route::post('/{id}/documents', \App\Actions\Jetstream\UploadDocForUser::class . '@upload')->name('users.documents.create')->where(['id' => '[0-9]+']);
    Route::get('/export', \App\Http\Controllers\UsersController::class . '@export')->name('users.export');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('teams')->group(function () {
    Route::get('/', \App\Http\Controllers\TeamController::class . '@index')->name('teams');
    Route::get('/create', \App\Http\Controllers\TeamController::class . '@create')->name('teams.create');
    Route::post('/', \App\Actions\Jetstream\CreateTeam::class)->name('teams.store');
    Route::get('/edit/{id}', \App\Http\Controllers\TeamController::class . '@edit')->name('teams.edit');
    Route::get('/view/{id}', \App\Http\Controllers\TeamController::class . '@view')->name('teams.view');
//    for some reason, the commented route below gets overridden by the default teams route
    //Route::put('/{id}', \App\Http\Controllers\TeamsController::class . '@update')->name('team.update');
    Route::post('/teams/{team}/members', \App\Http\Controllers\TeamMemberController::class . '@store')->name('team-member.store');
    Route::put('/update/{id}', \App\Actions\Jetstream\UpdateTeam::class)->name('team.update');
    Route::delete('/{id}', \App\Actions\Jetstream\DeleteTeam::class)->name('teams.delete');
    Route::delete('/{team}/{teamMemberId}', \App\Actions\Jetstream\RemoveTeamMember::class)->name('team-members.destroy');
    Route::get('/export', \App\Http\Controllers\TeamController::class . '@export')->name('teams.export');
});
Route::middleware(['auth:sanctum', 'verified'])->prefix('settings')->group(function () {
    Route::get('/', \App\Http\Controllers\ClientSettingsController::class . '@index')->name('settings');
    Route::post('/client-services', \App\Http\Controllers\ClientSettingsController::class . '@updateClientServices')->name('settings.client-services.update');
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
    Route::post('/users', \App\Actions\Impersonation\GetUsers::class)->name('impersonation.users');
});
Route::prefix('impersonation')->group(function () {
    Route::post('/on', \App\Actions\Impersonation\ImpersonateUser::class)->name('impersonation.start');
    Route::post('/off', \App\Actions\Impersonation\StopImpersonatingUser::class)->name('impersonation.stop');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('note')->group(function () {
    Route::post('/', \App\Actions\Fortify\MarkNoteAsRead::class)->name('note.seen');
});
Route::middleware(['auth:sanctum', 'verified'])->prefix('crud')->group(function () {
    Route::post('/', \App\Actions\Clients\SetCustomUserCrudColumns::class)->name('crud-customize');
});

Route::prefix('invite')->group(function () {
    Route::get('/{id}', \App\Http\Controllers\InviteController::class . '@index')->name('invite');
    Route::post('/accept', \App\Actions\Clients\Calendar\AcceptInvite::class)->name('invite.accept');
    Route::post('/decline', \App\Actions\Clients\Calendar\DeclineInvite::class)->name('invite.decline');
});
Route::middleware(['auth:sanctum', 'verified'])->prefix('notifications')->group(function () {
    Route::get('/', \App\Actions\Users\Notifications\GetNotifications::class)->name('notifications');
    Route::get('/unread', \App\Actions\Users\Notifications\GetUnreadNotificationCount::class)->name('notifications.unread');
    Route::post('/{id}', \App\Actions\Users\Notifications\DismissNotification::class)->name('notifications.dismiss');
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

Route::middleware(['auth:sanctum', 'verified'])->prefix('mass-com')->group(function () {
    Route::get('/', \App\Http\Controllers\MassCommunicationController::class . '@index')->name('mass_com.dashboard');
    Route::get('/{type}', \App\Http\Controllers\MassCommunicationController::class . '@page')->name('mass_com.page');
});
