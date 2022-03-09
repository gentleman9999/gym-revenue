<?php

namespace Database\Seeders\AccessControl;

use App\Models\CalendarEvent;
use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Endusers\Lead;
use App\Models\Team;
use App\Models\TeamDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Bouncer;
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
        $crud_models = collect(['users', 'locations', 'leads', 'files', 'teams', 'todo-list', 'calendar']);
        $operations = collect(['create', 'read', 'update', 'trash', 'restore', 'delete']);

        // Create the Full Unrestricted Abilities
        $crud_models->each(function ($crud_model) use ($operations) {
            $operations->each(function ($operation) use ($crud_model) {
                $title = ucwords("$operation $crud_model");
                Bouncer::ability()->firstOrCreate([
                    'name' => "$crud_model.$operation",
                    'title' => $title,
                ]);
            });
        });

        // Create user impersonation ability. It only applies to users.
        Bouncer::ability()->firstOrCreate([
            'name' => "users.impersonate",
            'title' => 'Impersonate Users',
        ]);

        /** Admin */
        Bouncer::allow('Admin')->everything(); // I mean....right?
        //$this->allowReadInGroup(['users', 'locations', 'leads', 'files', 'teams'], 'Admin');
        //$this->allowEditInGroup(['users', 'locations', 'files', 'teams'], 'Admin');

        /** Account Owner */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'files','teams', 'calendar'], 'Account Owner');
        $this->allowEditInGroup(['users', 'locations', 'leads', 'files','teams', 'calendar'], 'Account Owner');
        $this->allowImpersonationInGroup(['users'], 'Account Owner');

        /** Regional Admin */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'files','teams', 'calendar'], 'Regional Admin');
        $this->allowEditInGroup(['users', 'locations', 'leads', 'files','teams', 'calendar'], 'Regional Admin');
        $this->allowImpersonationInGroup(['users'], 'Regional Admin');

        /** Location Manager */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Location Manager');
        $this->allowEditInGroup(['users', 'leads', 'teams', 'todo-list', 'calendar'], 'Location Manager');
        $this->allowImpersonationInGroup(['users'], 'Location Manager');

        /** Sales Rep */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Sales Rep');
        $this->allowEditInGroup(['leads', 'todo-list', 'calendar'], 'Regional Admin');

        /** Employee */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Employee');
        $this->allowEditInGroup(['leads', 'todo-list'], 'Employee');

        /** Fitness Trainer */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Fitness Trainer');
        $this->allowEditInGroup(['leads', 'todo-list'], 'Employee');

        /** Personal Trainer */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Personal Trainer');
        $this->allowEditInGroup(['leads', 'todo-list'], 'Employee');

        /** Instructor */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Instructor');
        $this->allowEditInGroup(['leads', 'todo-list'], 'Employee');

        /** Fitness Manager */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Fitness Manager');
        $this->allowEditInGroup(['leads', 'todo-list'], 'Employee');

        /** Sanitation */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Sanitation');
        $this->allowEditInGroup(['leads', 'todo-list'], 'Employee');

        /** Human Resources */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Human Resources');
        $this->allowEditInGroup(['leads', 'todo-list'], 'Employee');

        /** Day Care Manager */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list', 'calendar'], 'Day Care Manager');
        $this->allowEditInGroup(['leads', 'todo-list'], 'Employee');


        $roles_allowed_to_contact_leads = ['Location Manager', 'Sales Rep', 'Employee'];
        foreach ($roles_allowed_to_contact_leads as $role) {
            VarDumper::dump("Allowing $role to contact leads for teams");
            Bouncer::allow($role)->to('leads.contact', Lead::class);
        }
        /*
        $this->teams->each(function ($team) use ($roles_allowed_to_contact_leads) {
            foreach ($roles_allowed_to_contact_leads as $role) {
                VarDumper::dump("Allowing $role to contact leads for $team->name");
                Bouncer::allow($role)->to('leads.contact', $team);
            }
        });
        */

    }

    protected function allowAllInGroup($group, $role)
    {
        $groups = collect($group);
        $groups->each(function ($group) use ($role) {
            VarDumper::dump("Allowing all on $group");
            $group_abilities = Bouncer::ability()->where('name', 'like', "$group.%");
            $group_abilities->each(function ($ability) use ($role) {
                Bouncer::allow($role)->to($ability->name);
            });
            /*
            VarDumper::dump("Allowing all on $group for $role");
            $this->teams->each(function ($team) use ($group, $role) {
                $group_abilities = Bouncer::ability()->where('name', 'like', "$group.%");
                $group_abilities->each(function ($ability) use ($role, $team) {
                    Bouncer::allow($role)->to($ability->name, $team);
                });
            });
            */
        });
    }

    protected function allowReadInGroup($group, $role)
    {
        /**
         * The Roles are Generic, that is, they are not client-specific should serve as a list of
         * abilities available to assign a Security Role. The Security Roles
         * will use these abilities to present the Account Owner with the ability to add flexibility
         * to the Security Role, which then in turn the Security Role assigned to the user
         * abilities will be assigned directly to the user and Full Ability disallowed
         */

        // Convert the $group array into a Collection
        $groups = collect($group);

        // Collection version of foreach item group and use the role
        $groups->each(function ($group) use ($role) {
            // Create and get the abilities for all the groups
            switch($group)
            {
                case 'users':
                    $entity = User::class;
                    break;
                case 'locations':
                    $entity = Location::class;
                    break;
                case 'leads':
                    $entity = Lead::class;
                    break;
                case 'teams':
                    $entity = Team::class;
                    break;

                case 'files':
                case 'todo-list':
                    $entity = null;
                    break;
                case 'calendar':
                    $entity = CalendarEvent::class;
                    break;

            }
            // Allow the role to inherit the not Ability in full, but scoped to the team
            if($entity)
            {
                VarDumper::dump("Allowing $role to $group.read $group");
                Bouncer::allow($role)->to("$group.read", $entity);
            }
            else
            {
                VarDumper::dump("Allowing $role to $group.read");
                Bouncer::allow($role)->to("$group.read");
            }
            /*
            // Cycle through each team and add the ability for each team and add it to the role
            $this->teams->each(function ($team) use ($group, $role) {
                // Create and get the abilities for all the groups
                $group_abilities = Bouncer::ability()->where('name', 'like', "$group.read%");
                // For each of those abilitys
                $group_abilities->each(function ($ability) use ($role, $team) {
                    // Tell it like it is preacher man
                    VarDumper::dump("Allowing $role to $ability->name for $team->name");
                    // Allow the role to inherit the not Ability in full, but scoped to the team
                    Bouncer::allow($role)->to($ability->name, $team);
                });
            });
            */
        });
    }

    protected function allowEditInGroup($group, $role)
    {
        $groups = collect($group);
        $groups->each(function ($group) use ($role) {
            switch($group)
            {
                case 'users':
                    $entity = User::class;
                    break;
                case 'locations':
                    $entity = Location::class;
                    break;
                case 'leads':
                    $entity = Lead::class;
                    break;
                case 'teams':
                    $entity = Team::class;
                    break;

                case 'files':
                case 'todo-list':
                    $entity = null;
                    break;
                case 'calendar':
                    $entity = CalendarEvent::class;
                    break;
            }
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
            else
            {
                VarDumper::dump("Allowing $role to $group.create");
                Bouncer::allow($role)->to("$group.create");
                VarDumper::dump("Allowing $role to $group.update");
                Bouncer::allow($role)->to("$group.update");
                VarDumper::dump("Allowing $role to $group.trash");
                Bouncer::allow($role)->to("$group.trash");
                VarDumper::dump("Allowing $role to $group.restore");
                Bouncer::allow($role)->to("$group.restore");
            }
            /*
            $this->teams->each(function ($team) use ($group, $role) {
                $group_abilities = Bouncer::ability()->where('name', 'like', "$group.create%");
                $group_abilities->each(function ($ability) use ($role, $team) {
                    VarDumper::dump("Allowing $role to $ability->name for $team->name");
                    Bouncer::allow($role)->to($ability->name, $team);
                });

                $group_abilities = Bouncer::ability()->where('name', 'like', "$group.update%");
                $group_abilities->each(function ($ability) use ($role, $team) {
                    VarDumper::dump("Allowing $role to $ability->name for $team->name");
                    Bouncer::allow($role)->to($ability->name, $team);
                });

                $group_abilities = Bouncer::ability()->where('name', 'like', "$group.trash%");
                $group_abilities->each(function ($ability) use ($role, $team) {
                    VarDumper::dump("Allowing $role to $ability->name for $team->name");
                    Bouncer::allow($role)->to($ability->name, $team);
                });

                $group_abilities = Bouncer::ability()->where('name', 'like', "$group.restore%");
                $group_abilities->each(function ($ability) use ($role, $team) {
                    VarDumper::dump("Allowing $role to $ability->name for $team->name");
                    Bouncer::allow($role)->to($ability->name, $team);
                });

                $group_abilities = Bouncer::ability()->where('name', 'like', "$group.delete%");
                $group_abilities->each(function ($ability) use ($role, $team) {
                    VarDumper::dump("Allowing $role to $ability->name for $team->name");
                    Bouncer::allow($role)->to($ability->name, $team);
                });

            });
            */
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
