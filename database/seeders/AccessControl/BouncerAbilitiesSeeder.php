<?php

declare(strict_types=1);

namespace Database\Seeders\AccessControl;

use App\Domain\Clients\Projections\Client;
use App\Domain\Roles\Role;
use App\Domain\Users\Models\EndUser;
use App\Domain\Users\Models\User;
use App\Services\Process;
use Illuminate\Database\Seeder;
use Silber\Bouncer\BouncerFacade as Bouncer;

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
        $crud_models = [
            'users',
            'locations',
            'endusers',
            'lead-statuses',
            'lead-sources',
            'files',
            'teams',
            'tasks',
            'calendar',
            'roles',
            'classifications',
            'access_tokens',
            'departments',
            'positions',
            'email-templates',
            'sms-templates',
            'scheduled-campaigns',
            'drip-campaigns',
            'reminders',
            'notes',
            'folders',
            'searches',
            'dynamic-reports',
            'call-templates',
            'conversation',
            'chat',
            'customers',
            'leads',
            'members',
            'employees',
            'entry-source-category',];
        $operations = collect(['create', 'read', 'update', 'trash', 'restore', 'delete']);

        // Create the Full Unrestricted Abilities

        $bouncers = [];
        foreach ($crud_models as $crud_model) {
            foreach ($operations as $operation) {
                $bouncers[] = [
                    'name' => "{$crud_model}.{$operation}",
                    'title' => ucwords("{$operation} {$crud_model}"),
                    'entity_type' => Role::getEntityFromGroup($crud_model),
                ];
            }
        }

        Bouncer::ability()->upsert($bouncers, ['name']);

        // Create user impersonation ability. It only applies to users.
        Bouncer::ability()->firstOrCreate([
            'name' => 'users.impersonate',
            'title' => 'Impersonate Users',
            'entity_type' => User::class,
        ]);

        $process = Process::allocate(5);
        $clients = Client::all();

        $impersonate = ['users'];
        $account_owner_read = [
            'users',
            'locations',
            'endusers',
            'lead-statuses',
            'lead-sources',
            'files',
            'teams',
            'calendar',
            'roles',
            'classifications',
            'access_tokens',
            'drip-campaigns',
            'scheduled-campaigns',
            'email-templates',
            'sms-templates',
            'call-templates',
            'departments',
            'positions',
            'notes',
            'folders',
            'dynamic-reports',
            'searches',
            'chat',
            'customers',
            'leads',
            'members',
            'employees',
        ];
        $account_owner_edit = [
            'users',
            'locations',
            'endusers',
            'lead-statuses',
            'lead-sources',
            'files',
            'teams',
            'calendar',
            'roles',
            'classifications',
            'access_tokens',
            'drip-campaigns',
            'scheduled-campaigns',
            'email-templates',
            'sms-templates',
            'call-templates',
            'departments',
            'positions',
            'notes',
            'folders',
            'dynamic-reports',
            'searches',
            'chat',
            'customers',
            'leads',
            'members',
            'employees',
        ];
        $regional_admin_read = [
            'users',
            'locations',
            'endusers',
            'files',
            'teams',
            'calendar',
            'roles',
            'classifications',
            'access_tokens',
            'drip-campaigns',
            'scheduled-campaigns',
            'email-templates',
            'sms-templates',
            'searches',
            'folders',
            'call-templates',
            'chat',
            'customers',
            'leads',
            'members',
            'employees',
        ];
        $regional_admin_edit = [
            'users',
            'locations',
            'endusers',
            'files',
            'teams',
            'calendar',
            'roles',
            'classifications',
            'access_tokens',
            'drip-campaigns',
            'scheduled-campaigns',
            'email-templates',
            'sms-templates',
            'folders',
            'call-templates',
            'chat',
            'customers',
            'leads',
            'members',
            'employees',
        ];
        $location_manager_read = [
            'users',
            'locations',
            'endusers',
            'teams',
            'tasks',
            'calendar',
            'access_tokens',
            'drip-campaigns',
            'scheduled-campaigns',
            'positions',
            'departments',
            'reminders',
            'searches',
            'email-templates',
            'sms-templates',
            'folders',
            'files',
            'call-templates',
            'chat',
            'customers',
            'leads',
            'members',
            'employees',
        ];
        $location_manager_edit = [
            'users',
            'endusers',
            'teams',
            'tasks',
            'calendar',
            'access_tokens',
            'drip-campaigns',
            'scheduled-campaigns',
            'positions',
            'departments',
            'reminders',
            'folders',
            'files',
            'chat',
            'customers',
            'leads',
            'members',
            'employees',
        ];
        $sale_rep_read = [
            'users',
            'locations',
            'endusers',
            'teams',
            'tasks',
            'calendar',
            'drip-campaigns',
            'scheduled-campaigns',
            'reminders',
            'folders',
            'searches',
            'files',
            'chat',
            'customers',
            'leads',
            'members',
            'employees',
        ];
        $sale_rep_edit = [
            'endusers',
            'tasks',
            'calendar',
            'reminders',
            'files',
            'folders',
            'chat',
            'customers',
            'leads',
            'members',
            'employees',
        ];
        $employee_read = [
            'users',
            'locations',
            'endusers',
            'teams',
            'tasks',
            'calendar',
            'reminders',
            'chat',
            'customers',
            'leads',
            'members',
            'employees',
        ];
        $employee_edit = [
            'endusers',
            'tasks',
            'chat',
            'customers',
            'leads',
            'members',
            'employees',
        ];
        $contact_user = ['Account Owner', 'Location Manager', 'Sales Rep', 'Employee'];

        $read_callback = [self::class, 'allowReadInGroup'];
        $edit_callback = [self::class, 'allowEditInGroup'];
        $impersonate_callback = [self::class, 'allowImpersonationInGroup'];

        foreach ($clients as $client) {
            /** Account Owner */
            $process->queue($read_callback, $account_owner_read, 'Account Owner', $client->id);
            $process->queue($edit_callback, $account_owner_edit, 'Account Owner', $client->id);

            $process->queue($impersonate_callback, $impersonate, 'Account Owner', $client->id);

            /** Regional Admin */
            $process->queue($read_callback, $regional_admin_read, 'Regional Admin', $client->id);
            $process->queue($edit_callback, $regional_admin_edit, 'Regional Admin', $client->id);
            $process->queue($impersonate_callback, $impersonate, 'Regional Admin', $client->id);

            /** Location Manager */
            $process->queue($read_callback, $location_manager_read, 'Location Manager', $client->id);
            $process->queue($edit_callback, $location_manager_edit, 'Location Manager', $client->id);
            $process->queue($impersonate_callback, $impersonate, 'Location Manager', $client->id);

            /** Sales Rep */
            $process->queue($read_callback, $sale_rep_read, 'Sales Rep', $client->id);
            $process->queue($edit_callback, $sale_rep_edit, 'Sales Rep', $client->id);

            /** Employee */
            $process->queue($read_callback, $employee_read, 'Employee', $client->id);
            $process->queue($edit_callback, $employee_edit, 'Employee', $client->id);

            $process->queue([self::class, 'contactUser'], $contact_user, $client->id);
        }

        $process->run();
        Bouncer::scope()->to(null);
    }

    public static function contactUser(array $groups, string $client_id): void
    {
        Bouncer::scope()->to($client_id);

        foreach ($groups as $role) {
            echo("Allowing $role to contact endusers for teams\n");
            Bouncer::allow($role)->to('endusers.contact', EndUser::class);
        }
        Bouncer::allow('Account Owner')->to('manage-client-settings');
    }

    public static function allowReadInGroup(array $groups, string $role, string $client_id): void
    {
        Bouncer::scope()->to($client_id);
        echo("Allowing $role read access\n");
        foreach ($groups as $group) {
            // Create and get the abilities for all the groups
            $entity = Role::getEntityFromGroup($group);
            // Allow the role to inherit the not Ability in full, but scoped to the team
            if ($entity) {
                Bouncer::allow($role)->to("{$group}.read", $entity);
            }
        }
    }

    public static function allowEditInGroup(array $groups, string $role, string $client_id): void
    {
        Bouncer::scope()->to($client_id);
        echo("Allowing $role write access\n");

        // Convert the $group array into a Collection
        foreach ($groups as $group) {
            $entity = Role::getEntityFromGroup($group);
            // Allow the role to inherit the not Ability in full, but scoped to the team
            if ($entity !== null) {
                Bouncer::allow($role)->to("{$group}.create", $entity);
                Bouncer::allow($role)->to("{$group}.update", $entity);
                Bouncer::allow($role)->to("{$group}.trash", $entity);
                Bouncer::allow($role)->to("{$group}.restore", $entity);
                Bouncer::allow($role)->to("{$group}.delete", $entity);
            }
        }
    }

    public static function allowImpersonationInGroup(array $groups, string $role, string $client_id): void
    {
        Bouncer::scope()->to($client_id);
        echo("Allowing $role impersonate access\n");

        foreach ($groups as $group) {
            Bouncer::allow($role)->to("{$group}.impersonate", User::class);
        }
    }
}
