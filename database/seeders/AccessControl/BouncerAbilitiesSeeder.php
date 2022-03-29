<?php

namespace Database\Seeders\AccessControl;

use App\Models\CalendarEvent;
use App\Models\Clients\Classification;
use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use App\Models\File;
use App\Models\Team;
use App\Models\TeamDetail;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Bouncer;
use Silber\Bouncer\Database\Role;
use Symfony\Component\VarDumper\VarDumper;

class BouncerAbilitiesSeeder extends Seeder
{
    protected $teams;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->teams = Team::all();
        $crud_models = collect(['users', 'locations', 'leads', 'files', 'teams', 'todo-list', 'calendar', 'roles', 'classifications']);
        $operations = collect(['create', 'read', 'update', 'trash', 'restore', 'delete']);

        // Create the Full Unrestricted Abilities
        $crud_models->each(function ($crud_model) use ($operations) {
            $operations->each(function ($operation) use ($crud_model) {
                $entity = \App\Models\Role::getEntityFromGroup($crud_model);
                $title = ucwords("$operation $crud_model");
                Bouncer::ability()->firstOrCreate([
                    'name' => "$crud_model.$operation",
                    'title' => $title,
                    'entity_type' => $entity
                ]);
            });
        });

        // Create user impersonation ability. It only applies to users.
        Bouncer::ability()->firstOrCreate([
            'name' => "users.impersonate",
            'title' => 'Impersonate Users',
            'entity_type' => User::class
        ]);

        /** Admin */
        Bouncer::allow('Admin')->everything(); // I mean....right?
        //$this->allowReadInGroup(['users', 'locations', 'leads', 'files', 'teams'], 'Admin');
        //$this->allowEditInGroup(['users', 'locations', 'files', 'teams'], 'Admin');

        /** Account Owner */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'files','teams', 'calendar', 'roles', 'classifications'], 'Account Owner');
        $this->allowEditInGroup(['users', 'locations', 'leads', 'files','teams', 'calendar', 'roles', 'classifications'], 'Account Owner');
        $this->allowImpersonationInGroup(['users'], 'Account Owner');

        /** Regional Admin */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'files', 'teams', 'calendar', 'roles', 'classifications'], 'Regional Admin');
        $this->allowEditInGroup(['users', 'locations', 'leads', 'files', 'teams', 'calendar', 'roles', 'classifications'], 'Regional Admin');
        $this->allowImpersonationInGroup(['users'], 'Regional Admin');

        /** Location Manager */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Location Manager');
        $this->allowEditInGroup(['users', 'leads', 'teams', 'todo-list', 'calendar'], 'Location Manager');
        $this->allowImpersonationInGroup(['users'], 'Location Manager');

        /** Sales Rep */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Sales Rep');
        $this->allowEditInGroup(['leads', 'todo-list', 'calendar'], 'Sales Rep');

        /** Employee */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Employee');
        $this->allowEditInGroup(['leads', 'todo-list'], 'Employee');

        $roles_allowed_to_contact_leads = ['Location Manager', 'Sales Rep', 'Employee'];
        foreach ($roles_allowed_to_contact_leads as $role) {
            VarDumper::dump("Allowing $role to contact leads for teams");
            Bouncer::allow($role)->to('leads.contact', Lead::class);
        }
    }

    protected function allowReadInGroup($group, $role)
    {
        // Convert the $group array into a Collection
        $groups = collect($group);

        // Collection version of foreach item group and use the role
        $groups->each(function ($group) use ($role) {
            // Create and get the abilities for all the groups
            $entity = \App\Models\Role::getEntityFromGroup($group);
            // Allow the role to inherit the not Ability in full, but scoped to the team
            if($entity)
            {
                VarDumper::dump("Allowing $role to read $group");
                Bouncer::allow($role)->to("$group.read", $entity);
            }
        });
    }

    protected function allowEditInGroup($group, $role)
    {
        // Convert the $group array into a Collection
        $groups = collect($group);

        // Collection version of foreach item group and use the role
        $groups->each(function ($group) use ($role) {
            $entity = \App\Models\Role::getEntityFromGroup($group);

            // Allow the role to inherit the not Ability in full, but scoped to the team
            if($entity)
            {
                VarDumper::dump("Allowing $role to $group.create");
                Bouncer::allow($role)->to("$group.create", $entity);
                VarDumper::dump("Allowing $role to $group.update");
                Bouncer::allow($role)->to("$group.update", $entity);
                VarDumper::dump("Allowing $role to $group.trash");
                Bouncer::allow($role)->to("$group.trash", $entity);
                VarDumper::dump("Allowing $role to $group.restore");
                Bouncer::allow($role)->to("$group.restore", $entity);
            }

        });
    }

    protected function allowImpersonationInGroup($group, $role)
    {
        $groups = collect($group);
        $groups->each(function ($group) use ($role) {
            switch ($group) {
                case 'users':
                    default:
                    $entity = User::class;
                    break;
            }
            // Allow the role to inherit the not Ability in full, but scoped to the team
            if ($entity) {
                VarDumper::dump("Allowing $role to $group.impersonate");
                Bouncer::allow($role)->to("$group.impersonate", $entity);
            }

        });

    }

}
