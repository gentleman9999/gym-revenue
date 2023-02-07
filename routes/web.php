<?php

use App\Domain\VoiceCalls\Actions\InitiateCall;
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
Route::middleware(['auth:sanctum', 'verified'])->put('/current-team/{team}', \App\Domain\Users\Actions\SwitchTeam::class)->name('current-team.update');
Route::middleware(['auth:sanctum', 'verified'])->put('/current-calendar-location', \App\Http\Controllers\LocationsController::class . '@switchCalendar')->name('current-location-qv.update');
//@todo: need to add in ACL/middleware for CnB users
Route::middleware(['auth:sanctum', 'verified'])->prefix('locations')->group(function () {
    Route::get('/', \App\Http\Controllers\LocationsController::class . '@index')->name('locations');
    Route::get('/create', \App\Http\Controllers\LocationsController::class . '@create')->name('locations.create');
    Route::get('/export', \App\Http\Controllers\LocationsController::class . '@export')->name('locations.export');
    Route::get('/{location}', \App\Http\Controllers\LocationsController::class . '@edit')->withTrashed()->name('locations.edit');
    Route::get('/view/{location}', \App\Http\Controllers\LocationsController::class . '@view')->withTrashed()->name('locations.view');
    Route::post('/', \App\Domain\Locations\Actions\CreateLocation::class)->name('locations.store');
    Route::post('/import', \App\Domain\Locations\Actions\ImportLocations::class)->name('locations.import');
    Route::put('/{location}', \App\Domain\Locations\Actions\UpdateLocation::class)->name('locations.update');
    Route::delete('/{location}', \App\Domain\Locations\Actions\CloseLocation::class)->name('locations.close');
    Route::post('/{location}/reopen', \App\Domain\Locations\Actions\ReopenLocation::class)->withTrashed()->name('locations.reopen');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('user')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\UserProfileController::class, 'show'])->name('profile.show');
    Route::post('/tokens', \App\Domain\Users\Actions\GrantAccessToken::class)->name('api-tokens.store');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('data')->group(function () {
    Route::prefix('leads')->group(function () {
        Route::get('/', \App\Http\Controllers\Data\LeadsController::class . '@index')->name('data.leads');
        Route::get('/claimed', \App\Http\Controllers\Data\LeadsController::class . '@claimed')
            ->name('data.leads.claimed');
        Route::get('/create', \App\Http\Controllers\Data\LeadsController::class . '@create')
            ->name('data.leads.create');
        Route::post('/create', \App\Domain\Users\Actions\CreateUser::class)->name('data.leads.store');
        Route::get('/show/{endUser}', \App\Http\Controllers\Data\LeadsController::class . '@show')
            ->name('data.leads.show');
        Route::get('/edit/{endUser}', \App\Http\Controllers\Data\LeadsController::class . '@edit')
            ->name('data.leads.edit');
        Route::put('/{user}', \App\Domain\Users\Actions\UpdateUser::class)->name('data.leads.update');
        Route::put('/assign/{end_user}', \App\Domain\Users\Actions\AssignEndUserToRep::class)
            ->name('data.leads.assign');
        Route::post('/contact/{end_user}', \App\Http\Controllers\Data\LeadsController::class . '@contact')
            ->name('data.leads.contact');
        Route::get('/sources', \App\Http\Controllers\Data\LeadsController::class . '@sources')
            ->name('data.entry.sources');
        Route::get('/sourceCategories', \App\Http\Controllers\EntrySourceCategoryController::class . '@index')
            ->name('data.entry.source.categories');
        Route::post('/sources/update', \App\Domain\EntrySources\Actions\UpdateEntrySources::class)
            ->name('data.entry.sources.update');
        Route::post(
            '/sourceCategories/update',
            \App\Domain\EntrySourceCategories\Actions\UpdateEntrySourceCategory::class
        )
            ->name('data.entry.source.category.update');
        Route::get('/statuses', \App\Http\Controllers\Data\LeadsController::class . '@statuses')
            ->name('data.leads.statuses');
        Route::post('/statuses/update', \App\Domain\LeadStatuses\Actions\UpdateLeadStatuses::class)
            ->name('data.leads.statuses.update');
        Route::delete('/terminate/{user}', \App\Domain\Users\Actions\TerminateUser::class)
            ->name('data.leads.terminate');
        Route::post('/terminate/{user}/reinstate', \App\Domain\Users\Actions\ReinstateUser::class)->withTrashed()
            ->name('data.leads.reinstate');
        Route::get('/view/{endUser}', \App\Http\Controllers\Data\LeadsController::class . '@view')
            ->name('data.leads.view');
        Route::get('/export', \App\Http\Controllers\Data\LeadsController::class . '@export')
            ->name('data.leads.export');
        Route::post('/upload', \App\Domain\Users\Actions\CreateFiles::class)->name('data.leads.upload');
        Route::post('/upload', \App\Domain\Users\Actions\CreateProfilePicture::class)
            ->name('data.leads.upload.profile.picture');
    });

    Route::prefix('members')->group(function () {
        Route::get('/', \App\Http\Controllers\Data\MembersController::class . '@index')->name('data.members');
        Route::get('/create', \App\Http\Controllers\Data\MembersController::class . '@create')
            ->name('data.members.create');
        // Route::post('/', \App\Domain\Users\Actions\CreateUser::class)->name('data.members.store');
        Route::get('/show/{endUser}', \App\Http\Controllers\Data\MembersController::class . '@show')
            ->name('data.members.show');
        Route::get('/edit/{endUser}', \App\Http\Controllers\Data\MembersController::class . '@edit')
            ->name('data.members.edit');
        Route::put('/{user}', \App\Domain\Users\Actions\UpdateUser::class)->name('data.members.update');
        Route::post('/contact/{end_user}', \App\Http\Controllers\Data\MembersController::class . '@contact')
            ->name('data.members.contact');
        Route::delete('/terminate/{user}', \App\Domain\Users\Actions\TerminateUser::class)
            ->name('data.members.terminate');
        Route::post('/terminate/{user}/reinstate', \App\Domain\Users\Actions\ReinstateUser::class)->withTrashed()
            ->name('data.members.reinstate');
        Route::get('/view/{endUser}', \App\Http\Controllers\Data\MembersController::class . '@view')
            ->name('data.members.view');
        Route::get('/export', \App\Http\Controllers\Data\MembersController::class . '@export')
            ->name('data.members.export');
        Route::post('/upload', \App\Domain\Users\Actions\CreateProfilePicture::class)
            ->name('data.members.upload.profile.picture');
    });

    Route::prefix('customers')->group(function () {
        Route::get('/', \App\Domain\Users\Actions\GetCustomers::class)->name('data.customers');
        Route::get('/create', \App\Domain\Users\Actions\GetCreateCustomer::class)->name('data.customers.create');
        // Route::post('/', \App\Domain\Users\Actions\CreateUser::class)->name('data.customers.store');
        Route::get('/show/{end_user}', \App\Domain\Users\Actions\ShowCustomer::class)->name('data.customers.show');
        Route::get('/edit/{end_user}', \App\Domain\Users\Actions\EditCustomer::class)->name('data.customers.edit');
        Route::put('/{user}', \App\Domain\Users\Actions\UpdateUser::class)->name('data.customers.update');
        Route::delete('/terminate/{user}', \App\Domain\Users\Actions\TerminateUser::class)
            ->name('data.customers.terminate');
        Route::post('/terminate/{user}/reinstate', \App\Domain\Users\Actions\ReinstateUser::class)->withTrashed()
            ->name('data.customers.reinstate');
        Route::get('/view/{end_user}', \App\Domain\Users\Actions\ViewCustomer::class)->name('data.customers.view');
        Route::get('/export', \App\Domain\Users\Actions\ExportCustomer::class)->name('data.customers.export');
    });

    Route::get('/conversions', \App\Http\Controllers\DashboardController::class . '@index')->name('data.conversions');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('entry-source-categories')->group(function () {
    Route::get('/', \App\Http\Controllers\EntrySourceCategoryController::class . '@index')
        ->name('entry-source-categories');
    Route::post('/', \App\Domain\EntrySourceCategories\Actions\CreateEntrySourceCategory::class)
        ->name('entry-source-categories.store');
    Route::get('/edit/{esc_id}', \App\Http\Controllers\EntrySourceCategoryController::class . '@edit')
        ->name('entry-source-categories.edit');
    Route::put('/{esc_id}', \App\Domain\EntrySourceCategories\Actions\UpdateEntrySourceCategory::class)
        ->name('entry-source-categories.update');
    Route::delete('/{esc_id}', \App\Domain\EntrySourceCategories\Actions\TrashEntrySourceCategory::class)
        ->name('entry-source-categories.trash');
    Route::delete('/{esc_id}/force', \App\Domain\EntrySourceCategories\Actions\DeleteEntrySourceCategory::class)
        ->name('entry-source-categories.delete');
    Route::post('/{esc_id}/restore', \App\Domain\EntrySourceCategories\Actions\RestoreEntrySourceCategory::class)
        ->name('entry-source-categories.restore');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('files')->group(function () {
    Route::get('/', \App\Http\Controllers\FilesController::class . '@index')->name('files');
    Route::get('/upload', \App\Http\Controllers\FilesController::class . '@upload')->name('files.upload');
    Route::post('/', \App\Domain\Files\Actions\CreateFiles::class)->name('files.store');
    Route::get('/edit/{id}', \App\Http\Controllers\FilesController::class . '@edit')->name('files.edit');
    Route::put('/{id}/rename', \App\Domain\Files\Actions\RenameFile::class)->name('files.rename');
    Route::put('/{id}', \App\Domain\Files\Actions\UpdateFilePermissions::class)->name('files.update');
    Route::put('/folder/{id}', \App\Domain\Files\Actions\UpdateFileFolder::class)->name('files.update.folder');
    Route::delete('/{id}', \App\Domain\Files\Actions\TrashFile::class)->name('files.trash');
    Route::delete('/{id}/force', \App\Domain\Files\Actions\DeleteFile::class)->name('files.delete');
    Route::post('/{id}/restore', \App\Domain\Files\Actions\RestoreFile::class)->name('files.restore');
    Route::get('/export', \App\Http\Controllers\FilesController::class . '@export')->name('files.export');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('folders')->group(function () {
    Route::get('/', \App\Http\Controllers\FoldersController::class . '@index')->name('folders');
    Route::post('/', \App\Domain\Folders\Actions\CreateFolder::class)->name('folders.store');
    Route::get('/viewFiles/{folder}', \App\Http\Controllers\FoldersController::class . '@viewFiles')
        ->name('folders.viewFiles');
    Route::put('/{folder}', \App\Domain\Folders\Actions\UpdateFolder::class)->name('folders.update');
    Route::put('/sharing/{folder}', \App\Domain\Folders\Actions\UpdateFolderSharing::class)
        ->name('folders.sharing.update');
    Route::delete('/{folder}/trash', \App\Domain\Folders\Actions\TrashFolder::class)->name('folders.trash');
    Route::delete('/{folder}', \App\Domain\Folders\Actions\DeleteFolder::class)->name('folders.delete');
    Route::post('/{id}/restore', \App\Domain\Folders\Actions\RestoreFolder::class)->name('folders.restore');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('reminders')->group(function () {
    Route::get('/', \App\Http\Controllers\RemindersController::class . '@index')->name('reminders');
    Route::get('/create', \App\Http\Controllers\RemindersController::class . '@create')->name('reminders.create');
    Route::post('/', \App\Domain\Reminders\Actions\CreateReminder::class)->name('reminders.store');
    Route::get('/edit/{reminder}', \App\Http\Controllers\RemindersController::class . '@edit')->name('reminders.edit');
    Route::put('/{reminder}', \App\Domain\Reminders\Actions\UpdateReminder::class)->name('reminders.update');
    Route::delete('/{reminder}', \App\Domain\Reminders\Actions\DeleteReminder::class)->name('reminders.delete');
});


Route::middleware(['auth:sanctum', 'verified'])->prefix('notes')->group(function () {
    Route::get('/', \App\Http\Controllers\NotesController::class . '@index')->name('notes');
    Route::get('/create', \App\Http\Controllers\NotesController::class . '@create')->name('notes.create');
    Route::post('/', \App\Domain\Notes\Actions\CreateNote::class)->name('notes.store');
    Route::get('/edit/{id}', \App\Http\Controllers\NotesController::class . '@edit')->name('notes.edit');
    Route::put('/{id}', \App\Domain\Notes\Actions\UpdateNote::class)->name('notes.update');
    Route::delete('/{id}/force', \App\Domain\Notes\Actions\DeleteNote::class)->name('notes.delete');
    Route::get('/export', \App\Http\Controllers\NotesController::class . '@export')->name('notes.export');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('searches')->group(function () {
    Route::get('/', \App\Http\Controllers\SearchController::class . '@index')->name('searches');
    Route::post('/searchahead', \App\Http\Controllers\SearchController::class . '@search_api')->name('searches.read');
//    Route::post('/', \App\Actions\Clients\Searches\CreateSearch::class)->name('searches.store');
//    Route::get('/edit/{id}', \App\Http\Controllers\SearchController::class . '@edit')->name('searches.edit');
//    Route::put('/{id}', \App\Actions\Clients\Searches\UpdateSearch::class)->name('searches.update');
//    Route::delete('/{id}/force', \App\Actions\Clients\Searches\DeleteSearch::class)->name('searches.delete');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('calendar')->group(function () {
    Route::get('/', \App\Http\Controllers\CalendarController::class . '@index')->name('calendar');
    Route::get('/mycalendar', \App\Http\Controllers\CalendarController::class . '@myCalendar')->name('calendar.mine');
    Route::get('/quickview', \App\Http\Controllers\CalendarController::class . '@quickView')
        ->name('calendar.quickview');
    Route::post('/', \App\Domain\CalendarEvents\Actions\CreateCalendarEvent::class)->name('calendar.event.store');
    Route::put('/{calendarEvent}', \App\Domain\CalendarEvents\Actions\UpdateCalendarEvent::class)
        ->name('calendar.event.update');
    Route::delete('/reminder/delete/{id}', \App\Domain\Reminders\Actions\DeleteReminder::class)
        ->name('calendar.reminder.delete');
    Route::put('/reminder/create/{id}', \App\Domain\Reminders\Actions\CreateReminderFromCalendarEvent::class)
        ->name('calendar.reminder.create');
    Route::put('/complete_task/{id}', \App\Actions\Clients\Tasks\MarkTaskComplete::class)
        ->name('calendar.complete_event');
    Route::post('/upload', \App\Domain\CalendarEvents\Actions\UploadFileToCalendarEvent::class)
        ->name('calendar.upload');
    Route::prefix('event_types')->group(function () {
        Route::get('/', \App\Http\Controllers\CalendarController::class . '@eventTypes')->name('calendar.event_types');
        Route::get('/create', \App\Http\Controllers\CalendarController::class . '@createEventType')
            ->name('calendar.event_types.create');
        Route::post('/', \App\Domain\CalendarEventTypes\Actions\CreateCalendarEventType::class)
            ->name('calendar.event_types.store');
        Route::get('/edit/{calendarEventType}', \App\Http\Controllers\CalendarController::class . '@editEventType')
        ->name('calendar.event_types.edit');
        Route::put('/{calendarEventType}', \App\Domain\CalendarEventTypes\Actions\UpdateCalendarEventType::class)
            ->name('calendar.event_types.update');
        Route::delete('/{calendarEventType}', \App\Domain\CalendarEventTypes\Actions\TrashCalendarEventType::class)
            ->name('calendar.event_types.trash');
        Route::delete(
            '/{calendarEventType}/force',
            \App\Domain\CalendarEventTypes\Actions\DeleteCalendarEventType::class
        )
            ->name('calendar.event_types.delete');
        Route::post(
            '/{calendarEventType}/restore',
            \App\Domain\CalendarEventTypes\Actions\RestoreCalendarEventType::class
        )->withTrashed()
            ->name('calendar.event_types.restore');
    });
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('users')->group(function () {
    Route::get('/', \App\Http\Controllers\UsersController::class . '@index')->name('users');
    Route::get('/create', \App\Http\Controllers\UsersController::class . '@create')->name('users.create');
    Route::post('/store', \App\Domain\Users\Actions\CreateUser::class)->name('users.store');
    Route::post('/', \App\Domain\Users\Actions\CreateFiles::class)->name('users.files.store');
    Route::post('/import', \App\Domain\Users\Actions\ImportUsers::class)->name('users.import');
    Route::get('/edit/{user}', \App\Http\Controllers\UsersController::class . '@edit')->name('users.edit');
    Route::get('/view/{user}', \App\Http\Controllers\UsersController::class . '@view')->name('users.view');
    Route::put('/{user}', \App\Domain\Users\Actions\UpdateUser::class)->name('users.update');
    Route::delete('delete/{user}', \App\Domain\Users\Actions\TerminateUser::class)->name('users.terminate');
    Route::post('/terminate/{user}/reinstate', \App\Domain\Users\Actions\ReinstateUser::class)->withTrashed()->name('users.reinstate');
    Route::post('/{user}/documents', \App\Actions\Teams\UploadDocForUser::class . '@upload')->name('users.documents.create');
    Route::get('/export', \App\Http\Controllers\UsersController::class . '@export')->name('users.export');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('teams')->group(function () {
    Route::get('/', \App\Http\Controllers\TeamController::class . '@index')->name('teams');
    Route::get('/create', \App\Http\Controllers\TeamController::class . '@create')->name('teams.create');
    Route::post('/', \App\Domain\Teams\Actions\CreateTeam::class)->name('teams.store');
    Route::get('/edit/{team}', \App\Http\Controllers\TeamController::class . '@edit')->name('teams.edit');
    Route::get('/view/{team}', \App\Http\Controllers\TeamController::class . '@view')->name('teams.view');
//    for some reason, the commented route below gets overridden by the default teams route
    Route::post('/{team}/members', \App\Domain\Teams\Actions\AddOrInviteTeamMembers::class)->name('team-member.store');
    Route::delete('/{team}/{teamMemberId}', \App\Domain\Teams\Actions\RemoveTeamMember::class)->name('team-members.destroy');
    Route::put('/update/{team}', \App\Domain\Teams\Actions\UpdateTeam::class)->name('team.update');
    Route::delete('/{team}', \App\Domain\Teams\Actions\DeleteTeam::class)->name('teams.delete');
    Route::get('/export', \App\Http\Controllers\TeamController::class . '@export')->name('teams.export');
});
Route::middleware(['auth:sanctum', 'verified'])->prefix('settings')->group(function () {
    Route::get('/', \App\Http\Controllers\ClientSettingsController::class . '@index')->name('settings');
    Route::post('/twilio-services', \App\Domain\Clients\Actions\SetClientServices::class)->name('settings.twilio-services.update');
    Route::post('/twilio-comms-prefs', \App\Domain\Clients\Actions\SetClientCommsPrefs::class)->name('settings.twilio-comms-prefs.update');
    Route::put('/social-media-set', \App\Domain\Clients\Actions\UpdateSocialMedias::class)->name('settings.social-media.update');
    Route::put('/gateway-set', \App\Domain\Clients\Actions\UpdateGateways::class)->name('settings.gateway.update');
    Route::post('/logo', \App\Domain\Clients\Actions\UploadLogo::class)->name('settings.logo.upload');
    Route::delete('/logo', \App\Domain\Clients\Actions\DeleteLogo::class)->name('settings.logo.delete');
    Route::post('/trial-memberships', \App\Http\Controllers\ClientSettingsController::class . '@updateTrialMembershipTypes')->name('settings.trial-membership-types.update');
    Route::post('/entry-source', \App\Domain\EntrySources\Actions\UpdateEntrySource::class)
        ->name('settings.client-entry-sources.update');
    Route::put('/entry-source-categories', \App\Domain\EntrySourceCategories\Actions\UpdateEntrySourceCategory::class)
        ->name('settings.client-entry-source-categories.update');
    Route::post(
        '/entry-source-categories/create',
        \App\Domain\EntrySourceCategories\Actions\CreateEntrySourceCategory::class
    )
        ->name('settings.client-entry-source-categories.store');
    Route::delete('/{id}', \App\Domain\EntrySourceCategories\Actions\TrashEntrySourceCategory::class)
        ->name('settings.client-entry-source-categories.delete');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('roles')->group(function () {
    Route::get('/', \App\Http\Controllers\RolesController::class . '@index')->name('roles');
    Route::get('/create', \App\Http\Controllers\RolesController::class . '@create')->name('roles.create');
    Route::post('/', \App\Domain\Roles\Actions\CreateRole::class)->name('roles.store');
    Route::get('/edit/{role}', \App\Http\Controllers\RolesController::class . '@edit')->name('roles.edit');
    Route::put('/{role}', \App\Domain\Roles\Actions\UpdateRole::class)->name('roles.update');
    Route::delete('/{role}', \App\Domain\Roles\Actions\DeleteRole::class)->name('roles.delete');
    Route::get('/export', \App\Http\Controllers\RolesController::class . '@export')->name('roles.export');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('departments')->group(function () {
    Route::get('/', \App\Http\Controllers\DepartmentsController::class . '@index')->name('departments');
    Route::get('/create', \App\Http\Controllers\DepartmentsController::class . '@create')->name('departments.create');
    Route::post('/', \App\Domain\Departments\Actions\CreateDepartment::class)->name('departments.store');
    Route::get('/edit/{department}', \App\Http\Controllers\DepartmentsController::class . '@edit')->name('departments.edit');
    Route::put('/{department}', \App\Domain\Departments\Actions\UpdateDepartment::class)->name('departments.update');
    Route::delete('/{department}', \App\Domain\Departments\Actions\TrashDepartment::class)->name('departments.trash');
    Route::delete('/{department}/force', \App\Domain\Departments\Actions\DeleteDepartment::class)->name('departments.delete');
    Route::post('/{department}/restore', \App\Domain\Departments\Actions\RestoreDepartment::class)->name('departments.restore');
    Route::get('/export', \App\Http\Controllers\DepartmentsController::class . '@export')->name('departments.export');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('positions')->group(function () {
    Route::get('/', \App\Http\Controllers\PositionsController::class . '@index')->name('positions');
    Route::get('/create', \App\Http\Controllers\PositionsController::class . '@create')->name('positions.create');
    Route::post('/', \App\Domain\Positions\Actions\CreatePosition::class)->name('positions.store');
    Route::get('/edit/{position}', \App\Http\Controllers\PositionsController::class . '@edit')->name('positions.edit');
    Route::put('/{position}', \App\Domain\Positions\Actions\UpdatePosition::class)->name('positions.update');
    Route::delete('/{position}', \App\Domain\Positions\Actions\TrashPosition::class)->name('positions.trash');
    Route::delete('/{position}/force', \App\Domain\Positions\Actions\DeletePosition::class)->name('positions.delete');
    Route::post('/{position}/restore', \App\Domain\Positions\Actions\RestorePosition::class)->withTrashed()->name('positions.restore');
    Route::get('/export', \App\Http\Controllers\PositionsController::class . '@export')->name('positions.export');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('tasks')->group(function () {
    Route::get('/', \App\Http\Controllers\TaskController::class . '@index')->name('tasks');
    Route::delete('/{id}', \App\Domain\CalendarEvents\Actions\DeleteCalendarEvent::class)->name('tasks.delete');
    Route::post('/', \App\Domain\Campaigns\Actions\CreateCallOutcome::class)->name('tasks.call-outcome');
    Route::post('/update', \App\Domain\Campaigns\Actions\UpdateCallOutcome::class)->name('tasks.call-outcome.update');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('impersonation')->group(function () {
    Route::post('/users', \App\Domain\Users\Actions\GetUsersToImpersonate::class)->name('impersonation.users');
    Route::get('/users', \App\Domain\Users\Actions\GetUsersToImpersonate::class)->name('impersonation.users');
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
    Route::post('{calendarEvent}/accept', \App\Domain\CalendarAttendees\Actions\AcceptInvite::class)->name('invite.accept');
    Route::post('{calendarEvent}/decline', \App\Domain\CalendarAttendees\Actions\DeclineInvite::class)->name('invite.decline');
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
Route::middleware(['auth:sanctum', 'verified'])->prefix('mass-comms')->group(function () {
    Route::get('template-tokens', \App\Domain\Templates\Actions\TemplateParser::class)->name('mass-comms.template.tokens');
    Route::get('/campaigns/{type?}', \App\Http\Controllers\MassCommunicationController::class . '@campaignDash')->name('mass-comms.campaigns.dashboard');
    Route::prefix('audiences')->group(function () {
        Route::put('/{audience}', \App\Domain\Audiences\Actions\UpdateAudience::class)->name('mass-comms.audiences.update');
        Route::post('/', \App\Domain\Audiences\Actions\CreateAudience::class)->name('mass-comms.audiences.create');
    });
    Route::prefix('scheduledCampaigns')->group(function () {
        Route::post('/', \App\Domain\Campaigns\ScheduledCampaigns\Actions\CreateScheduledCampaign::class)->name('mass-comms.scheduled-campaigns.store');
        Route::put('/{scheduledCampaign}', \App\Domain\Campaigns\ScheduledCampaigns\Actions\UpdateScheduledCampaign::class)->name('mass-comms.scheduled-campaigns.update');
        Route::delete('/{scheduledCampaign}', \App\Domain\Campaigns\ScheduledCampaigns\Actions\TrashScheduledCampaign::class)->name('mass-comms.scheduled-campaigns.trash');
        Route::post('/{scheduledCampaign}/restore', \App\Domain\Campaigns\ScheduledCampaigns\Actions\RestoreScheduledCampaign::class)->withTrashed()->name('mass-comms.scheduled-campaigns.restore');
        Route::get('/{scheduledCampaign}', \App\Http\Controllers\MassCommunicationController::class . '@getScheduledCampaign')->name('mass-comms.scheduled-campaigns.get');
    });
    Route::prefix('dripCampaigns')->group(function () {
        Route::put('/{dripCampaign}', \App\Domain\Campaigns\DripCampaigns\Actions\UpdateDripCampaign::class)->name('mass-comms.drip-campaigns.update');
        Route::post('/', \App\Domain\Campaigns\DripCampaigns\Actions\CreateDripCampaign::class)->name('mass-comms.drip-campaigns.store');
        Route::get('/{dripCampaign}', \App\Http\Controllers\MassCommunicationController::class . '@getDripCampaign')->name('mass-comms.drip-campaigns.get');
        Route::delete('/{dripCampaign}', \App\Domain\Campaigns\DripCampaigns\Actions\TrashDripCampaign::class)->name('mass-comms.drip-campaigns.trash');
        Route::post('/{dripCampaign}/restore', \App\Domain\Campaigns\DripCampaigns\Actions\RestoreDripCampaign::class)->withTrashed()->name('mass-comms.drip-campaigns.restore');
    });
    Route::prefix('sms-templates')->group(function () {
        Route::get('', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@index')->name('mass-comms.sms-templates');
        Route::get('/create', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@create')->name('mass-comms.sms-templates.create');
        Route::get('/export', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@export')->name('mass-comms.sms-templates.export');
        Route::get('/{smsTemplate}', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@edit')->name('mass-comms.sms-templates.edit');
        Route::post('/', \App\Domain\Templates\SmsTemplates\Actions\CreateSmsTemplate::class)->name('mass-comms.sms-templates.store');
        Route::post('/{smsTemplate}', \App\Domain\Templates\SmsTemplates\Actions\DuplicateSmsTemplate::class)->name('mass-comms.sms-templates.duplicate');
        Route::put('/{smsTemplate}', \App\Domain\Templates\SmsTemplates\Actions\UpdateSmsTemplate::class)->name('mass-comms.sms-templates.update');
        Route::delete('/{smsTemplate}', \App\Domain\Templates\SmsTemplates\Actions\TrashSmsTemplate::class)->name('mass-comms.sms-templates.trash');
        Route::post('/{smsTemplate}/restore', \App\Domain\Templates\SmsTemplates\Actions\RestoreSmsTemplate::class)->withTrashed()->name('mass-comms.sms-templates.restore');
        Route::post('/test', \App\Domain\SMS\Actions\FireTestMessage::class)->name('mass-comms.sms-templates.test-msg');
    });
    Route::prefix('email-templates')->group(function () {
        Route::get('/', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@index')->name('mass-comms.email-templates');
        Route::post('/', \App\Domain\Templates\EmailTemplates\Actions\CreateEmailTemplate::class)->name('mass-comms.email-templates.store');
        Route::get('/create', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@create')->name('mass-comms.email-templates.create');
        Route::get('/export', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@export')->name('mass-comms.email-templates.export');
        Route::post('/test', \App\Domain\Email\Actions\FireTestEmailMessage::class)->name('mass-comms.email-templates.test-msg');
        Route::get('/getfiles', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@getFiles')->name('mass-comms.email-templates.get-files');

        Route::post('block', \App\Domain\Templates\EmailTemplateBlocks\Actions\CreateEmailTemplateBlock::class)->name('comms.email-templates.create-block');
        Route::get('block', \App\Domain\Templates\EmailTemplateBlocks\Actions\GetEmailTemplateBlock::class)->name('comms.email-templates.get-blocks');
        Route::delete('block/{block}', \App\Domain\Templates\EmailTemplateBlocks\Actions\DeleteEmailTemplateBlock::class)->name('comms.email-templates.delete-block');
        Route::put('block/{block}', \App\Domain\Templates\EmailTemplateBlocks\Actions\UpdateEmailTemplateBlock::class)->name('comms.email-templates.update-block');

        Route::post('/{emailTemplate}/restore', \App\Domain\Templates\EmailTemplates\Actions\RestoreEmailTemplate::class)->withTrashed()->name('mass-comms.email-templates.restore');
        Route::get('/{emailTemplate}', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@edit')->name('mass-comms.email-templates.edit');
        Route::put('/{emailTemplate}', \App\Domain\Templates\EmailTemplates\Actions\UpdateEmailTemplate::class)->name('mass-comms.email-templates.update');
        Route::delete('/{emailTemplate}', \App\Domain\Templates\EmailTemplates\Actions\TrashEmailTemplate::class)->name('mass-comms.email-templates.trash');
        Route::post('/restore', \App\Domain\Templates\EmailTemplates\Actions\RestoreEmailTemplate::class)->withTrashed()->name('mass-comms.email-templates.restore');
        Route::post('/test', \App\Domain\Email\Actions\FireTestEmailMessage::class)->name('mass-comms.email-templates.test-msg');
    });
    Route::prefix('call-templates')->group(function () {
        Route::get('/', \App\Http\Controllers\Comm\CallScriptTemplatesController::class . '@index')->name('mass-comms.call-templates');
        Route::get('/create', \App\Http\Controllers\Comm\CallScriptTemplatesController::class . '@create')->name('mass-comms.call-templates.create');
        Route::get('/duplicate/{call_template}', \App\Http\Controllers\Comm\CallScriptTemplatesController::class . '@create')->name('mass-comms.call-templates.duplicate');
        Route::get('/export', \App\Http\Controllers\Comm\CallScriptTemplatesController::class . '@export')->name('mass-comms.call-templates.export');
        Route::get('/{call_template}', \App\Http\Controllers\Comm\CallScriptTemplatesController::class . '@edit')->name('mass-comms.call-templates.edit');
        Route::post('/', \App\Domain\Templates\CallScriptTemplates\Actions\CreateCallScriptTemplate::class)->name('mass-comms.call-templates.store');
        Route::put('/{call_template}', \App\Domain\Templates\CallScriptTemplates\Actions\UpdateCallScriptTemplate::class)->name('mass-comms.call-templates.update');
        Route::delete('/{call_template}', \App\Domain\Templates\CallScriptTemplates\Actions\TrashCallScriptTemplate::class)->name('mass-comms.call-templates.trash');
        Route::post('/{call_template}/restore', \App\Domain\Templates\CallScriptTemplates\Actions\RestoreCallScriptTemplate::class)->withTrashed()->name('mass-comms.call-templates.restore');
    });
    Route::get('/{type?}', \App\Http\Controllers\MassCommunicationController::class . '@index')->name('mass-comms.dashboard');
});


Route::middleware(['auth:sanctum', 'verified'])->prefix('templates')->group(function () {
    Route::get('/', \App\Http\Controllers\TemplatesController::class . '@index')->name('templates');
    Route::get('/sms-templates', \App\Http\Controllers\Comm\SmsTemplatesController::class . '@index')->name('mass-comms.sms-templates');
    Route::get('/email-templates', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@index')->name('mass-comms.email-templates');
    Route::get('/email-templates/images', \App\Http\Controllers\Comm\EmailTemplatesController::class . '@getFiles')->name('mass-comms.email-templates.get-files');
    Route::post('/email-templates/images', \App\Domain\Templates\EmailTemplateBlocks\Actions\UploadFile::class)->name('mass-comms.email-templates.store-files');
    Route::get('/call-templates', \App\Http\Controllers\Comm\CallScriptTemplatesController::class . '@index')->name('mass-comms.call-templates');
});


Route::prefix('dynamicreports')->group(function () {
    Route::get('/', \App\Http\Controllers\DynamicReportsController::class . '@show')->name('dynamic-reports');
    Route::get('/show', \App\Http\Controllers\DynamicReportsController::class . '@index')->name('dynamic-reports.show');
    Route::get('/create', \App\Http\Controllers\DynamicReportsController::class . '@create')->name('dynamic-reports.create');
    Route::post('/', \App\Domain\DynamicReports\Actions\CreateReport::class)->name('dynamic-reports.store');
    Route::get('/edit/{dynamicReport}', \App\Http\Controllers\DynamicReportsController::class . '@edit')->name('dynamic-reports.edit');
    Route::put('/{dynamicReport}', \App\Domain\DynamicReports\Actions\UpdateReport::class)->name('dynamic-reports.update');
    Route::delete('/{dynamicReport}', \App\Domain\Departments\Actions\TrashDepartment::class)->name('dynamic-reports.trash');
    Route::delete('/{dynamicReport}/force', \App\Domain\Departments\Actions\DeleteDepartment::class)->name('dynamic-reports.delete');
    Route::get('/users', \App\Domain\DynamicReports\Actions\CreateUserReport::class)->name('dr.user');
    Route::get('/leads', \App\Domain\DynamicReports\Actions\CreateLeadReport::class)->name('dr.lead');
    Route::get('/members', \App\Domain\DynamicReports\Actions\CreateMemberReport::class)->name('dr.member');
    Route::get('/leadexport', \App\Domain\DynamicReports\Export\ExportLeadReport::class)->name('dr.exportlead');
});

Route::middleware('auth:sanctum')->get('/clients', \App\Domain\Clients\Actions\GetClients::class)->name('clients');
Route::middleware('auth:sanctum')->get('/clients/teams', \App\Domain\Clients\Actions\GetTeams::class)->name('clients.teams');

Route::middleware('auth:sanctum')->prefix('chat')->group(function () {
    Route::prefix('internals')->group(static function () {
        Route::post('create', \App\Domain\Chat\Actions\CreateChat::class)->name('start-internal-chats');
        Route::get('get', \App\Domain\Chat\Actions\GetChat::class)->name('get-internal-chats');
        Route::get('interlocutors', \App\Domain\Chat\Actions\GetInterlocutor::class)->name('get-internal-chats-interlocutors');
        Route::put('message/{chat_message}', \App\Domain\Chat\Actions\UpdateMessage::class)->name('edit-internal-chat');
        Route::delete('message/{chat_message}', \App\Domain\Chat\Actions\DeleteMessage::class)->name('delete-internal-chat');
        Route::post('message', \App\Domain\Chat\Actions\CreateMessage::class)->name('message-internal-chats');
        Route::post('participant/{chat}', \App\Domain\Chat\Actions\CreateParticipant::class)->name('add-chat-participant');
        Route::delete('participant/{chat_participant}', \App\Domain\Chat\Actions\DeleteChatParticipant::class)->name('delete-chat-participant');
    });

    Route::get('/', \App\Http\Controllers\ChatController::class . '@index')->name('chat');
    Route::get('/{end_user_type}/{id}', \App\Http\Controllers\ChatController::class . '@index')->name('end-user-chat');
});

Route::middleware('auth:sanctum')->prefix('call')->group(static function () {
    Route::get('initialize/{phone}/type/{type}', InitiateCall::class)->name('twilio.call.initialize')
        ->whereIn('type', [InitiateCall::TYPE_LEAD, InitiateCall::TYPE_MEMBER]);
    Route::get('status/{sid}', \App\Domain\VoiceCalls\Actions\GetStatus::class)->name('twilio.call.status');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('conversation')->group(static function () {
    Route::prefix('twilio')->group(static function () {
        Route::get('get-api-token', \App\Domain\Conversations\Twilio\Actions\GetChatAccessToken::class)->name('twilio.api-token');
        Route::get('get-conversation', \App\Domain\Conversations\Twilio\Actions\GetConversation::class)->name('twilio.get-conversation');
        Route::put('conversation/{conversation_id}', \App\Domain\Conversations\Twilio\Actions\UpdateConversation::class)->name('twilio.update-conversation');
    });
});

Route::prefix('checkin')->group(function () {
    Route::get('/', \App\Http\Controllers\CheckInController::class . '@index')->name('checkin.index');
    Route::get('/login', \App\Http\Controllers\CheckInController::class . '@login')->name('checkin.login');
    Route::get('/register', \App\Http\Controllers\CheckInController::class . '@register')->name('checkin.register');
    Route::get('/account', \App\Http\Controllers\CheckInController::class . '@account')->name('checkin.account');
    Route::get('/club', \App\Http\Controllers\CheckInController::class . '@club')->name('checkin.club');
    Route::get('/result', \App\Http\Controllers\CheckInController::class . '@result')->name('checkin.result');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('contracts')->group(function () {
    Route::post('/', \App\Domain\Contracts\Actions\CreateContract::class)->name('contract.store');
    Route::put('/{contract_id}', \App\Domain\Contracts\Actions\UpdateContract::class)->name('contract.update');
    Route::delete('/{contract_id}', \App\Domain\Contracts\Actions\TrashContract::class)->name('contract.trash');
    Route::delete('/{contract_id}/force', \App\Domain\Contracts\Actions\DeleteContract::class)->name('contract.delete');
    Route::post('/{contract_id}/restore', \App\Domain\Contracts\Actions\RestoreContract::class)->withTrashed()->name('contract.restore');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('member-group')->group(function () {
    Route::post('/', \App\Domain\MemberGroups\Actions\CreateMemberGroup::class)->name('member-group-store');
    Route::put('/{id}', \App\Domain\MemberGroups\Actions\UpdateMemberGroup::class)->name('member-group.update');
    Route::delete('/{id}', \App\Domain\MemberGroups\Actions\TrashMemberGroup::class)->name('member-group.trash');
    Route::delete('/{id}/force', \App\Domain\MemberGroups\Actions\DeleteMemberGroup::class)->name('member-group.delete');
    Route::get('/{id}/restore', \App\Domain\MemberGroups\Actions\RestoreMemberGroup::class)->withTrashed()->name('member-group.restore');
});


Route::middleware(['auth:sanctum', 'verified'])->prefix('user-member-group')->group(function () {
    Route::post('/', \App\Domain\UserMemberGroups\Actions\CreateUserMemberGroup::class)->name('user-member-group.store');
    Route::put('/{id}', \App\Domain\UserMemberGroups\Actions\UpdateUserMemberGroup::class)->name('user-member-group.update');
    Route::delete('/{id}/force', \App\Domain\UserMemberGroups\Actions\DeleteUserMemberGroup::class)->name('user-member-group.delete');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('structured-documents')->group(function () {
    Route::post('/', \App\Domain\StructuredDocuments\Actions\CreateStructuredDocument::class)->name('structured-document.store');
    Route::put('/{structuredDocument}', \App\Domain\StructuredDocuments\Actions\UpdateStructuredDocument::class)->name('structured-document.update');
    Route::delete('/{structured_document_id}', \App\Domain\StructuredDocuments\Actions\TrashStructuredDocument::class)->name('structured-document.trash');
    Route::delete('/{structured_document_id}/force', \App\Domain\StructuredDocuments\Actions\DeleteStructuredDocument::class)->name('structured-document.delete');
    Route::post('/{structuredDocument}/restore', \App\Domain\StructuredDocuments\Actions\RestoreStructuredDocument::class)->withTrashed()->name('structured-document.restore');

    Route::prefix('files')->group(function () {
        Route::post('/', \App\Domain\StructuredDocuments\StructuredDocumentFiles\Actions\CreateStructuredDocumentFile::class)->name('structured-documents.files.store');
        Route::put('/{structuredDocumentFile}', \App\Domain\StructuredDocuments\StructuredDocumentFiles\Actions\UpdateStructuredDocumentFile::class)->name('structured-documents.files.update');
        Route::delete('/{structured_document_file_id}', \App\Domain\StructuredDocuments\StructuredDocumentFiles\Actions\TrashStructuredDocumentFile::class)->name('structured-documents.files.trash');
        Route::delete('/{structured_document_file_id}/force', \App\Domain\StructuredDocuments\StructuredDocumentFiles\Actions\DeleteStructuredDocumentFile::class)->name('structured-documents.files.delete');
        Route::post('/{structuredDocumentFile}/restore', \App\Domain\StructuredDocuments\StructuredDocumentFiles\Actions\RestoreStructuredDocumentFile::class)->withTrashed()->name('structured-documents.files.restore');
    });
});


Route::middleware(['auth:sanctum', 'verified'])->prefix('agreement')->group(function () {
    Route::post('/pdf/sign/{agreement_id}', \App\Domain\Agreements\Actions\SignAgreement::class)->name('agreement.pdf.sign');
    Route::get('/pdf/{agreement_id}', \App\Http\Controllers\AgreementController::class . '@viewAgreementPDF')->name('agreement.pdf');
});

Route::middleware(['auth:sanctum', 'verified'])->prefix('structured-document-requests')->group(function () {
    Route::post('/', \App\Domain\StructuredDocumentRequests\Actions\CreateStructuredDocumentRequest::class)->name('structured-document-request.store');
    Route::put('/{structured_document_request}', \App\Domain\StructuredDocumentRequests\Actions\UpdateStructuredDocumentRequest::class)->name('structured-document-request.update');
    Route::delete('/{structured_document_request_id}', \App\Domain\StructuredDocumentRequests\Actions\TrashStructuredDocumentRequest::class)->name('structured-document-request.trash');
    Route::delete('/{structured_document_request_id}/force', \App\Domain\StructuredDocumentRequests\Actions\DeleteStructuredDocumentRequest::class)->name('structured-document-request.delete');
    Route::post('/{structured_document_request}/restore', \App\Domain\StructuredDocumentRequests\Actions\RestoreStructuredDocumentRequest::class)->withTrashed()->name('structured-document-request.restore');
});
