<?php

namespace App\Providers;

use App\Actions\Jetstream\AddTeamMember;
use App\Actions\Jetstream\CreateTeam;
use App\Actions\Jetstream\DeleteTeam;
use App\Actions\Jetstream\DeleteUser;
use App\Actions\Jetstream\InviteTeamMember;
use App\Actions\Jetstream\RemoveTeamMember;
use App\Actions\Jetstream\UpdateTeamName;
use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use App\Models\File;
use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;
use Silber\Bouncer\BouncerFacade as Bouncer;

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
    }

    /**
     * Configure the roles and permissions that are available within the application.
     *
     * @return void
     */
    protected function configurePermissions()
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        /***************************
         * START BOUNCER PERMISSIONS
         **************************/

        /** Admin Roles */

        /* USER */
        Bouncer::allow('Admin')->to('create', User::class);
        Bouncer::allow('Admin')->to('view', User::class);
        Bouncer::allow('Admin')->to('edit', User::class);
        Bouncer::allow('Admin')->to('delete', User::class);

        /* LEADS */
        Bouncer::allow('Admin')->to('create', Lead::class);
        Bouncer::allow('Admin')->to('view', Lead::class);
        Bouncer::disallow('Admin')->to('contact', Lead::class);
        Bouncer::disallow('Admin')->to('edit', Lead::class);
        Bouncer::allow('Admin')->to('delete', Lead::class);

        /* LOCATIONS/Club Mgmt */
        Bouncer::allow('Admin')->to('create', Location::class);
        Bouncer::allow('Admin')->to('view', Location::class);
        Bouncer::allow('Admin')->to('edit', Location::class);
        Bouncer::allow('Admin')->to('trash', Location::class);

        /* OTHER */
        Bouncer::allow('Admin')->to('view-todo-list');
        Bouncer::allow('Admin')->to('view-file-manager', File::class);

        /** Account Owner Roles */

        /* USER */
        Bouncer::allow('Account Owner')->to('create', User::class);
        Bouncer::allow('Account Owner')->to('view', User::class);
        Bouncer::allow('Account Owner')->to('edit', User::class);
        Bouncer::allow('Account Owner')->to('delete', User::class);

        /* LEADS */
        Bouncer::allow('Account Owner')->to('create', Lead::class);
        Bouncer::allow('Account Owner')->to('view', Lead::class);
        Bouncer::disallow('Account Owner')->to('contact', Lead::class);
        Bouncer::disallow('Account Owner')->to('edit', Lead::class);
        Bouncer::allow('Account Owner')->to('delete', Lead::class);

        /* LOCATIONS/Club Mgmt */
        Bouncer::allow('Account Owner')->to('create', Location::class);
        Bouncer::allow('Account Owner')->to('view', Location::class);
        Bouncer::allow('Account Owner')->to('edit', Location::class);
        Bouncer::allow('Account Owner')->to('trash', Location::class);

        /* OTHER */
        Bouncer::disallow('Account Owner')->to('view-todo-list');
        Bouncer::disallow('Account Owner')->to('view-file-manager', File::class);

        /** Location Manager Roles */

        /* USER */
        Bouncer::disallow('Location Manager')->to('create', User::class);
        Bouncer::disallow('Location Manager')->to('view', User::class);
        Bouncer::disallow('Location Manager')->to('edit', User::class);
        Bouncer::disallow('Location Manager')->to('delete', User::class);

        /* LEADS */
        Bouncer::allow('Location Manager')->to('create', Lead::class);
        Bouncer::allow('Location Manager')->to('view', Lead::class);
        Bouncer::allow('Location Manager')->to('contact', Lead::class);
        Bouncer::allow('Location Manager')->to('edit', Lead::class);
        Bouncer::allow('Location Manager')->to('delete', Lead::class);

        /* LOCATIONS/Club Mgmt */
        Bouncer::disallow('Location Manager')->to('create', Location::class);
        Bouncer::allow('Location Manager')->to('view', Location::class);
        Bouncer::disallow('Location Manager')->to('edit', Location::class);
        Bouncer::disallow('Location Manager')->to('trash', Location::class);

        /* OTHER */
        Bouncer::disallow('Location Manager')->to('view-todo-list');
        Bouncer::disallow('Location Manager')->to('view-file-manager', File::class);

        /** Sales Rep Roles */

        /* USER */
        Bouncer::disallow('Sales Rep')->to('create', User::class);
        Bouncer::disallow('Sales Rep')->to('view', User::class);
        Bouncer::disallow('Sales Rep')->to('edit', User::class);
        Bouncer::disallow('Sales Rep')->to('delete', User::class);

        /* LEADS */
        Bouncer::allow('Sales Rep')->to('create', Lead::class);
        Bouncer::allow('Sales Rep')->to('view', Lead::class);
        Bouncer::allow('Sales Rep')->to('contact', Lead::class);
        Bouncer::allow('Sales Rep')->to('edit', Lead::class);
        Bouncer::allow('Sales Rep')->to('delete', Lead::class);

        /* LOCATIONS/Club Mgmt */
        Bouncer::disallow('Sales Rep')->to('create', Location::class);
        Bouncer::allow('Sales Rep')->to('view', Location::class);
        Bouncer::disallow('Sales Rep')->to('edit', Location::class);
        Bouncer::disallow('Sales Rep')->to('trash', Location::class);

        /* OTHER */
        Bouncer::disallow('Sales Rep')->to('view-todo-list');
        Bouncer::disallow('Sales Rep')->to('view-file-manager', File::class);

        /** Employee Roles (Read Only for now) */

        /* USER */
        Bouncer::disallow('Employee')->to('create', User::class);
        Bouncer::disallow('Employee')->to('view', User::class);
        Bouncer::disallow('Employee')->to('edit', User::class);
        Bouncer::disallow('Employee')->to('delete', User::class);

        /* LEADS */
        Bouncer::disallow('Employee')->to('create', Lead::class);
        Bouncer::allow('Employee')->to('view', Lead::class);
        Bouncer::disallow('Employee')->to('contact', Lead::class);
        Bouncer::disallow('Employee')->to('edit', Lead::class);
        Bouncer::disallow('Employee')->to('delete', Lead::class);

        /* LOCATIONS/Club Mgmt */
        Bouncer::disallow('Employee')->to('create', Location::class);
        Bouncer::allow('Employee')->to('view', Location::class);
        Bouncer::disallow('Employee')->to('edit', Location::class);
        Bouncer::disallow('Employee')->to('trash', Location::class);

        /* OTHER */
        Bouncer::disallow('Employee')->to('view-todo-list');
        Bouncer::disallow('Employee')->to('view-file-manager', File::class);

        /***************************
         * END BOUNCER PERMISSIONS
         **************************/

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
    }
}
