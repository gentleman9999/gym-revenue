<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use App\Models\User;
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
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configurePermissions();

        Jetstream::createTeamsUsing(CreateTeam::class);
        Jetstream::updateTeamNamesUsing(UpdateTeamName::class);
        Jetstream::addTeamMembersUsing(AddTeamMember::class);
        Jetstream::inviteTeamMembersUsing(InviteTeamMember::class);
        Jetstream::removeTeamMembersUsing(RemoveTeamMember::class);
        Jetstream::deleteTeamsUsing(DeleteTeam::class);
        Jetstream::deleteUsersUsing(DeleteUser::class);
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user &&
                Hash::check($request->password, $user->password)) {
                //Let's set some info in our signed cookie so that
                // we can have some stateless session info
                session(['user_id' => $user->id]);
                session(['client_id' => $user->client_id]);

                return $user;
            }
        });
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
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
