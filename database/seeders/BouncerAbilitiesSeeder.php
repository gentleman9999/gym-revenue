<?php

namespace Database\Seeders;

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
        $crud_models = collect(['users', 'locations', 'leads', 'files', 'teams', 'todo-list']);
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

        /** Admin */
        Bouncer::allow('Admin')->everything(); // I mean....right?
        //$this->allowReadInGroup(['users', 'locations', 'leads', 'files', 'teams'], 'Admin');
        //$this->allowEditInGroup(['users', 'locations', 'files', 'teams'], 'Admin');

        /** Account Owner */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams'], 'Account Owner');
        $this->allowEditInGroup(['users', 'locations', 'teams'], 'Account Owner');

        /** Location Manager */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list'], 'Location Manager');
        $this->allowEditInGroup(['users', 'leads', 'teams', 'todo-list'], 'Location Manager');

        /** Regional Admin */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams'], 'Regional Admin');
        //$this->allowEditInGroup(, 'Regional Admin');

        /** Sales Rep */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list'], 'Sales Rep');
        $this->allowEditInGroup(['leads', 'todo-list'], 'Regional Admin');

        /** Employee */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'teams', 'todo-list'], 'Employee');


        $roles_allowed_to_contact_leads = ['Location Manager', 'Sales Rep'];
        foreach ($roles_allowed_to_contact_leads as $role) {
            VarDumper::dump("Allowing $role to contact leads for teams");
            Bouncer::allow($role)->to('leads.contact', Team::class);
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
                case 'todo-list':
                    $entity = null;
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
                case 'todo-list':
                    $entity = null;
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

}
