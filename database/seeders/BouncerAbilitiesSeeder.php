<?php

namespace Database\Seeders;

use App\Models\Clients\Client;
use App\Models\Team;
use App\Models\TeamDetail;
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


        $crud_models->each(function ($crud_model) use ($operations) {
            $operations->each(function ($operation) use ($crud_model) {
                $title = ucwords("$operation $crud_model");
                Bouncer::ability()->firstOrCreate([
                    'name' => "$crud_model.$operation",
//                    'name' => "{$crud_model}-$operation",
                    'title' => $title,
                ]);
            });
        });

        /** Admin */
        $this->allowReadInGroup(['users', 'locations', 'leads', 'files', 'teams'], 'Admin');
        $this->allowEditInGroup(['users', 'locations', 'files', 'teams'], 'Admin');

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
        $this->teams->each(function ($team) use ($roles_allowed_to_contact_leads) {
            foreach ($roles_allowed_to_contact_leads as $role) {
                VarDumper::dump("Allowing $role to contact leads for $team->name");
                Bouncer::allow($role)->to('leads.contact', $team);
            }
        });

    }

    protected function allowAllInGroup($group, $role)
    {
        $groups = collect($group);
        $groups->each(function ($group) use ($role) {
            $this->teams->each(function ($team) use ($group, $role) {
                $group_abilities = Bouncer::ability()->where('name', 'like', "$group.%");
                $group_abilities->each(function ($ability) use ($role, $team) {
                    VarDumper::dump("Allowing $role to $ability->name for $team->name");
                    Bouncer::allow($role)->to($ability->name, $team);
                });
            });
        });
    }

    protected function allowReadInGroup($group, $role)
    {
        $groups = collect($group);
        $groups->each(function ($group) use ($role) {
            $this->teams->each(function ($team) use ($group, $role) {
                $group_abilities = Bouncer::ability()->where('name', 'like', "$group.read%");
                $group_abilities->each(function ($ability) use ($role, $team) {
                    VarDumper::dump("Allowing $role to $ability->name for $team->name");
                    Bouncer::allow($role)->to($ability->name, $team);
                });
            });
        });
    }

    protected function allowEditInGroup($group, $role)
    {
        $groups = collect($group);
        $groups->each(function ($group) use ($role) {
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
        });
    }

}
