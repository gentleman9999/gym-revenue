<?php

namespace Database\Seeders\AccessControl;

use App\Domain\Clients\Models\Client;
use App\Domain\Leads\Models\Lead;
use App\Domain\Roles\Role;
use App\Domain\Users\Models\User;
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

        $crud_models = collect([
            'users', 'locations', 'leads', 'lead-statuses', 'lead-sources', 'members',
            'files', 'teams', 'tasks', 'calendar', 'roles', 'access_tokens', 'departments',
            'positions', 'email-templates', 'scheduled-campaigns', 'drip-campaigns', 'reminders',
        ]);
        $operations = collect(['create', 'read', 'update', 'trash', 'restore', 'delete']);

        // Create the Full Unrestricted Abilities
        $crud_models->each(function ($crud_model) use ($operations) {
            $operations->each(function ($operation) use ($crud_model) {
                $entity = Role::getEntityFromGroup($crud_model);
                $title = ucwords("$operation $crud_model");
                Bouncer::ability()->firstOrCreate([
                    'name' => "$crud_model.$operation",
                    'title' => $title,
                    'entity_type' => $entity,
                ]);
            });
        });

        // Create user impersonation ability. It only applies to users.
        Bouncer::ability()->firstOrCreate([
            'name' => "users.impersonate",
            'title' => 'Impersonate Users',
            'entity_type' => User::class,
        ]);

        $clients = Client::all();
        foreach ($clients as $client) {
            Bouncer::scope()->to($client->id);
            VarDumper::dump("Bouncer scoping to $client->name");

            /** Account Owner */
            $this->allowReadInGroup([
                'users', 'locations', 'leads', 'lead-statuses', 'lead-sources', 'members', 'files', 'teams',
                'calendar', 'roles', 'classifications', 'access_tokens', 'drip-campaigns', 'scheduled-campaigns',
            ], 'Account Owner', $client);
            $this->allowEditInGroup([
                'users', 'locations', 'leads', 'lead-statuses', 'lead-sources', 'members', 'files', 'teams', 'calendar',
                'roles', 'classifications', 'access_tokens', 'drip-campaigns', 'scheduled-campaigns',
            ], 'Account Owner', $client);

            $this->allowImpersonationInGroup(['users'], 'Account Owner', $client);

            /** Regional Admin */
            $this->allowReadInGroup(['users', 'locations', 'leads', 'members', 'files', 'teams', 'calendar', 'roles', 'classifications', 'access_tokens', 'drip-campaigns', 'scheduled-campaigns'], 'Regional Admin', $client);
            $this->allowEditInGroup(['users', 'locations', 'leads', 'members', 'files', 'teams', 'calendar', 'roles', 'classifications', 'access_tokens', 'drip-campaigns', 'scheduled-campaigns'], 'Regional Admin', $client);
            $this->allowImpersonationInGroup(['users'], 'Regional Admin', $client);

            /** Location Manager */
            $this->allowReadInGroup(['users', 'locations', 'leads', 'members', 'teams', 'tasks', 'calendar', 'access_tokens', 'drip-campaigns', 'scheduled-campaigns', 'positions', 'departments', 'reminders'], 'Location Manager', $client);
            $this->allowEditInGroup(['users', 'leads', 'teams', 'tasks', 'calendar', 'access_tokens', 'drip-campaigns', 'scheduled-campaigns', 'positions', 'departments', 'reminders'], 'Location Manager', $client);
            $this->allowImpersonationInGroup(['users'], 'Location Manager', $client);

            /** Sales Rep */
            $this->allowReadInGroup(['users', 'locations', 'leads', 'members', 'teams', 'tasks', 'calendar', 'drip-campaigns', 'scheduled-campaigns', 'reminders'], 'Sales Rep', $client);
            $this->allowEditInGroup(['leads', 'tasks', 'calendar', 'reminders'], 'Sales Rep', $client);

            /** Employee */
            $this->allowReadInGroup(['users', 'locations', 'leads', 'members', 'teams', 'tasks', 'calendar', 'reminders'], 'Employee', $client);
            $this->allowEditInGroup(['leads', 'tasks'], 'Employee', $client);

            $roles_allowed_to_contact_leads = ['Account Owner', 'Location Manager', 'Sales Rep', 'Employee'];
            foreach ($roles_allowed_to_contact_leads as $role) {
                VarDumper::dump("Allowing $role to contact leads for teams");
                Bouncer::allow($role)->to('leads.contact', Lead::class);
            }
            Bouncer::allow('Account Owner')->to('manage-client-settings');
        }
        Bouncer::scope()->to(null);
    }

    protected function allowReadInGroup($group, $role, $client)
    {
        VarDumper::dump("Allowing $role read access");
        // Convert the $group array into a Collection
        $groups = collect($group);

        // Collection version of foreach item group and use the role
        $groups->each(function ($group) use ($role, $client) {
            // Create and get the abilities for all the groups
            $entity = Role::getEntityFromGroup($group);
            // Allow the role to inherit the not Ability in full, but scoped to the team
            if ($entity) {
                Bouncer::allow($role)->to("$group.read", $entity);
            }
        });
    }

    protected function allowEditInGroup($group, $role, $client)
    {
        VarDumper::dump("Allowing $role write access");

        // Convert the $group array into a Collection
        $groups = collect($group);

        // Collection version of foreach item group and use the role
        $groups->each(function ($group) use ($role, $client) {
            $entity = Role::getEntityFromGroup($group);

            // Allow the role to inherit the not Ability in full, but scoped to the team
            if ($entity) {
                Bouncer::allow($role)->to("$group.create", $entity);
                Bouncer::allow($role)->to("$group.update", $entity);
                Bouncer::allow($role)->to("$group.trash", $entity);
                Bouncer::allow($role)->to("$group.restore", $entity);
                Bouncer::allow($role)->to("$group.delete", $entity);
            }
        });
    }

    protected function allowImpersonationInGroup($group, $role, $client)
    {
        VarDumper::dump("Allowing $role impersonate access");

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
                Bouncer::allow($role)->to("$group.impersonate", $entity);
            }
        });
    }
}
