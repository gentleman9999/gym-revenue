<?php

namespace Database\Seeders\AccessControl;

use App\Models\Clients\Client;
use App\Models\Endusers\Lead;
use App\Models\User;
use Bouncer;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class BouncerAbilitiesSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** Admin */
        Bouncer::allow('Admin')->everything(); // I mean....right?

        $crud_models = collect(['users', 'locations', 'leads', 'files', 'teams', 'todo-list', 'calendar', 'roles', 'classifications', 'tasks']);
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

        $clients = Client::all();
        foreach ($clients as $client) {
            Bouncer::scope()->to($client->id);

            /** Account Owner */
            $this->allowReadInGroup(['users', 'locations', 'leads', 'files', 'teams', 'calendar', 'roles', 'classifications', 'tasks'], 'Account Owner', $client);
            $this->allowEditInGroup(['users', 'locations', 'leads', 'files', 'teams', 'calendar', 'roles', 'classifications','tasks'], 'Account Owner', $client);
            $this->allowImpersonationInGroup(['users'], 'Account Owner', $client);

            /** Regional Admin */
            $this->allowReadInGroup(['users', 'locations', 'leads', 'files', 'teams', 'calendar', 'roles', 'classifications', 'tasks'], 'Regional Admin', $client);
            $this->allowEditInGroup(['users', 'locations', 'leads', 'files', 'teams', 'calendar', 'roles', 'classifications', 'tasks'], 'Regional Admin', $client);
            $this->allowImpersonationInGroup(['users'], 'Regional Admin', $client);

            /** Location Manager */
            $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar', 'tasks'], 'Location Manager', $client);
            $this->allowEditInGroup(['users', 'leads', 'teams', 'todo-list', 'calendar','tasks'], 'Location Manager', $client);
            $this->allowImpersonationInGroup(['users'], 'Location Manager', $client);

            /** Sales Rep */
            $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar', 'tasks'], 'Sales Rep', $client);
            $this->allowEditInGroup(['leads', 'todo-list', 'calendar','tasks'], 'Sales Rep', $client);

            /** Employee */
            $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar', 'tasks'], 'Employee', $client);
            $this->allowEditInGroup(['leads', 'todo-list'], 'Employee', $client);

            $roles_allowed_to_contact_leads = ['Location Manager', 'Sales Rep', 'Employee'];
            foreach ($roles_allowed_to_contact_leads as $role) {
                VarDumper::dump("Allowing $role to contact leads for teams");
                Bouncer::allow($role)->to('leads.contact', Lead::class);
            }
        }
        Bouncer::scope()->to(null);
    }

    protected function allowReadInGroup($group, $role, $client)
    {
        // Convert the $group array into a Collection
        $groups = collect($group);

        // Collection version of foreach item group and use the role
        $groups->each(function ($group) use ($role, $client) {
            // Create and get the abilities for all the groups
            $entity = \App\Models\Role::getEntityFromGroup($group);
            // Allow the role to inherit the not Ability in full, but scoped to the team
            if ($entity) {
                VarDumper::dump("Allowing $role to read $group");
                Bouncer::allow($role)->to("$group.read", $entity);
            }
        });
    }

    protected function allowEditInGroup($group, $role, $client)
    {
        // Convert the $group array into a Collection
        $groups = collect($group);

        // Collection version of foreach item group and use the role
        $groups->each(function ($group) use ($role, $client) {
            $entity = \App\Models\Role::getEntityFromGroup($group);

            // Allow the role to inherit the not Ability in full, but scoped to the team
            if ($entity) {
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

    protected function allowImpersonationInGroup($group, $role, $client)
    {
        $groups = collect($group);
        $groups->each(function ($group) use ($role, $client) {
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
