<?php

namespace Database\Seeders;

use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;

class BouncerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
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
        //Bouncer::disallow('Employee')->to('create', User::class);
        //Bouncer::disallow('Employee')->to('view', User::class);
        //Bouncer::disallow('Employee')->to('edit', User::class);
        //Bouncer::disallow('Employee')->to('delete', User::class);

        /* LEADS */
        //Bouncer::disallow('Employee')->to('create', Lead::class);
        //Bouncer::allow('Employee')->to('view', Lead::class);
        //Bouncer::disallow('Employee')->to('contact', Lead::class);
        //Bouncer::disallow('Employee')->to('edit', Lead::class);
        //Bouncer::disallow('Employee')->to('delete', Lead::class);

        /* LOCATIONS/Club Mgmt */
        //Bouncer::disallow('Employee')->to('create', Location::class);
        //Bouncer::allow('Employee')->to('view', Location::class);
        //Bouncer::disallow('Employee')->to('edit', Location::class);
        //Bouncer::disallow('Employee')->to('trash', Location::class);

        /* OTHER */
        //Bouncer::disallow('Employee')->to('view-todo-list');
        //Bouncer::disallow('Employee')->to('view-file-manager', File::class);

        /***************************
         * END BOUNCER PERMISSIONS
         **************************/
    }
}
