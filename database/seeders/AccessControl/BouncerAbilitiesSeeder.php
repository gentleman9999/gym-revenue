<?php

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
        ];
        $operations  = ['create', 'read', 'update', 'trash', 'restore', 'delete'];

        // Create the Full Unrestricted Abilities

        $bouncers = [];
        foreach ($crud_models as $crud_model) {
            foreach ($operations as $operation) {
                $bouncers[] = [
                    'name'        => "{$crud_model}.{$operation}",
                    'title'       => ucwords("{$operation} {$crud_model}"),
                    'entity_type' => Role::getEntityFromGroup($crud_model),
                ];
            }
        }

        Bouncer::ability()->upsert($bouncers, ['name']);

        // Create user impersonation ability. It only applies to users.
        Bouncer::ability()->firstOrCreate([
            'name'        => 'users.impersonate',
            'title'       => 'Impersonate Users',
            'entity_type' => User::class,
        ]);

        $process = Process::allocate(5);
        $clients = Client::all();
        foreach ($clients as $client) {
            Bouncer::scope()->to($client->id);
            echo("Bouncer scoping to {$client->name}\n");

            /** Account Owner */
            $process->queue([self::class, 'allowReadInGroup'], [
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
            ], 'Account Owner');
            $process->queue([self::class, 'allowEditInGroup'], [
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
            ], 'Account Owner');

            $process->queue([self::class, 'allowImpersonationInGroup'], ['users'], 'Account Owner');

            /** Regional Admin */
            $process->queue([self::class, 'allowReadInGroup'], [
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
                    'employees'
                ], 'Regional Admin');
            $process->queue([self::class, 'allowEditInGroup'], [
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
                ], 'Regional Admin');
            $process->queue([self::class, 'allowImpersonationInGroup'], ['users'], 'Regional Admin');

            /** Location Manager */
            $process->queue([self::class, 'allowReadInGroup'], [
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
            ], 'Location Manager');
            $process->queue([self::class, 'allowEditInGroup'], [
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
            ], 'Location Manager');
            $process->queue([self::class, 'allowImpersonationInGroup'], ['users'], 'Location Manager');

            /** Sales Rep */
            $process->queue([self::class, 'allowReadInGroup'], [
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
            ], 'Sales Rep');
            $process->queue([self::class, 'allowEditInGroup'], [
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
            ], 'Sales Rep');

            /** Employee */
            $process->queue([self::class, 'allowReadInGroup'], [
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
            ], 'Employee');
            $process->queue([self::class, 'allowEditInGroup'], [
                'endusers',
                'tasks',
                'chat',
                'customers',
                'leads',
                'members',
                'employees',
            ], 'Employee');

            $process->queue([self::class, 'contactUser'], ['Account Owner', 'Location Manager', 'Sales Rep', 'Employee']);
        }

        $process->run();
        Bouncer::scope()->to(null);
    }

    public static function contactUser(array $groups): void
    {
        foreach ($groups as $role) {
            echo("Allowing $role to contact endusers for teams\n");
            Bouncer::allow($role)->to('endusers.contact', EndUser::class);
        }
        Bouncer::allow('Account Owner')->to('manage-client-settings');
    }

    public static function allowReadInGroup(array $groups, string $role): void
    {
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

    public static function allowEditInGroup(array $groups, string $role): void
    {
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

    public static function allowImpersonationInGroup(array $groups, string $role): void
    {
        echo("Allowing $role impersonate access\n");

        foreach ($groups as $group) {
            Bouncer::allow($role)->to("{$group}.impersonate", User::class);
        }
    }
}
