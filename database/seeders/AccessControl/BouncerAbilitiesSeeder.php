<?php

namespace Database\Seeders\AccessControl;

use App\Domain\Clients\Projections\Client;
use App\Domain\Roles\Role;
use App\Domain\Users\Models\EndUser;
use App\Domain\Users\Models\User;
use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;
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
            'users', 'locations', 'endusers', 'lead-statuses', 'lead-sources',
            'files', 'teams', 'tasks', 'calendar', 'roles', 'access_tokens', 'departments',
            'positions', 'email-templates', 'sms-templates', 'scheduled-campaigns', 'drip-campaigns',
            'reminders', 'notes', 'folders', 'searches', 'dynamic-reports', 'call-templates', 'conversation', 'chat', 'customers',
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
                'users', 'locations', 'endusers', 'lead-statuses', 'lead-sources', 'files', 'teams',
                'calendar', 'roles', 'classifications', 'access_tokens', 'drip-campaigns', 'scheduled-campaigns',
                'email-templates', 'sms-templates', 'call-templates', 'departments', 'positions', 'notes', 'folders',
                'dynamic-reports', 'searches', 'chat', 'customers', 'leads', 'members', 'employees',
            ], 'Account Owner', $client);
            $this->allowEditInGroup([
                'users', 'locations', 'endusers', 'lead-statuses', 'lead-sources', 'files', 'teams',
                'calendar', 'roles', 'classifications', 'access_tokens', 'drip-campaigns', 'scheduled-campaigns',
                'email-templates', 'sms-templates', 'call-templates', 'departments', 'positions', 'notes', 'folders',
                'dynamic-reports', 'searches', 'chat', 'customers', 'leads', 'members', 'employees',
            ], 'Account Owner', $client);

            $this->allowImpersonationInGroup(['users'], 'Account Owner', $client);

            /** Regional Admin */
            $this->allowReadInGroup(
                ['users', 'locations', 'endusers', 'files', 'teams', 'calendar', 'roles',
                    'classifications', 'access_tokens', 'drip-campaigns', 'scheduled-campaigns', 'email-templates',
                    'sms-templates', 'searches', 'folders', 'call-templates', 'chat', 'customers', 'leads', 'members', 'employees'],
                'Regional Admin',
                $client
            );
            $this->allowEditInGroup(
                ['users', 'locations', 'endusers', 'files', 'teams', 'calendar', 'roles',
                    'classifications', 'access_tokens', 'drip-campaigns', 'scheduled-campaigns', 'email-templates',
                    'sms-templates', 'folders', 'call-templates', 'chat', 'customers', 'leads', 'members', 'employees',],
                'Regional Admin',
                $client
            );
            $this->allowImpersonationInGroup(['users'], 'Regional Admin', $client);

            /** Location Manager */
            $this->allowReadInGroup(['users', 'locations', 'endusers', 'teams', 'tasks', 'calendar', 'access_tokens',
                'drip-campaigns', 'scheduled-campaigns', 'positions', 'departments', 'reminders', 'searches', 'email-templates',
                'sms-templates', 'folders', 'files', 'call-templates', 'chat', 'customers', 'leads', 'members', 'employees',], 'Location Manager', $client);
            $this->allowEditInGroup(['users', 'endusers', 'teams', 'tasks', 'calendar', 'access_tokens', 'drip-campaigns',
                'scheduled-campaigns', 'positions', 'departments', 'reminders', 'folders', 'files', 'chat', 'customers', 'leads', 'members', 'employees',], 'Location Manager', $client);
            $this->allowImpersonationInGroup(['users'], 'Location Manager', $client);

            /** Sales Rep */
            $this->allowReadInGroup(['users', 'locations', 'endusers', 'teams', 'tasks', 'calendar', 'drip-campaigns',
                'scheduled-campaigns', 'reminders', 'folders', 'searches', 'files', 'chat', 'customers', 'leads', 'members', 'employees',
            ], 'Sales Rep', $client);
            $this->allowEditInGroup(['endusers', 'tasks', 'calendar', 'reminders', 'files', 'folders', 'chat', 'customers', 'leads', 'members', 'employees',], 'Sales Rep', $client);

            /** Employee */
            $this->allowReadInGroup(['users', 'locations', 'endusers', 'teams', 'tasks', 'calendar', 'reminders', 'chat', 'customers', 'leads', 'members', 'employees',], 'Employee', $client);
            $this->allowEditInGroup(['endusers', 'tasks', 'chat', 'customers', 'leads', 'members', 'employees',], 'Employee', $client);

            $roles_allowed_to_contact_endusers = ['Account Owner', 'Location Manager', 'Sales Rep', 'Employee'];
            foreach ($roles_allowed_to_contact_endusers as $role) {
                VarDumper::dump("Allowing $role to contact endusers for teams");
                Bouncer::allow($role)->to('endusers.contact', EndUser::class);
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
