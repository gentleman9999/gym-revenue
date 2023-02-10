<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Teams\Actions\AddTeamMember;
use App\Domain\Teams\Actions\CreateTeam;
use App\Domain\Teams\Actions\DeleteTeam;
use App\Domain\Teams\Actions\InviteTeamMember;
use App\Domain\Teams\Actions\RemoveTeamMember;
use App\Domain\Teams\Actions\UpdateTeamName;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\Models\TeamInvitation;
use App\Domain\Users\Actions\DeleteUser;
use App\Domain\Users\Models\User;
use App\Enums\UserTypesEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     */
    public function boot(): void
    {
        $this->configurePermissions();

        Jetstream::useUserModel(User::class);
        Jetstream::useTeamModel(Team::class);
        Jetstream::useTeamInvitationModel(TeamInvitation::class);

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
        Fortify::authenticateUsing(function (Request $request) {
            /** Call withoutGlobalScopes so we don't try to apply client_id on login */
            $users = User::withoutGlobalScopes()->where('email', $request->email)->get();
            $user  = null;
            foreach ($users as $maybe_user) {
                if (Hash::check($request->password, $maybe_user->password)) {
                    $user = $maybe_user;

                    break;
                }
            }

            if (
                $user &&
                Hash::check($request->password, $user->password)
            ) {
                /**
                 * Successfull CRM Auth -
                 * Let's set some info in our cookie session so we
                 * can use it in middleware / global scopes
                 * without having to hit the db
                 */

                if ($user->user_type == UserTypesEnum::EMPLOYEE) {
                    $team = $user->defaultTeam;
                    if ($team !== null) {
                        /**
                         * Set current_locaiton_id as null
                         * @TODO: Need to update it as default_team location
                         * After relationship between team and location has been set
                         */
                        session()->put('current_location_id', null);
                        session()->put('current_team_id', $team->id);
                        session()->put(
                            'current_team',
                            [
                                'id' => $team->id,
                                'name' => $team->name,
                                'client_id' => $team->client_id,
                            ]
                        );
                    }
                }

                session()->put('client_id', $user->client_id);
                session()->put('user_id', $user->id);

                return $user;
            }
        });
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        // @todo - utilize bouncer to get the roles (possibly using the session)
        //TODO:pull from roles table
        Jetstream::role('Admin', 'Administrator', [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Administrator users can perform any action.');

        Jetstream::role('Account Owner', 'Account Owner', [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Account owners can perform any action.');

        Jetstream::role('Regional Admin', 'Regional Admin', [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Regional Admins can perform any action for locations in their region.');

        Jetstream::role('Location Manager', 'Location Manager', [
            'create',
            'read',
            'update',
            'delete',
        ])->description('Location managers can perform any action for their location.');

        Jetstream::role('Sales Rep', 'Sales Rep', [
            'read',
            'create',
            'update',
        ])->description('Sales Reps have the ability to read, create, and update.');

        Jetstream::role('Employee', 'Employee', [
            'read',
        ])->description('Employees have the ability to read.');
    }
}
