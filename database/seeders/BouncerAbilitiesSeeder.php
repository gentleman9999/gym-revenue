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
        $crud_models = collect(['users', 'locations', 'leads', 'files', 'teams']);
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
        $this->allowAllInGroup($crud_models, 'Admin');
        $this->allowAllInGroup($crud_models, 'Account Owner');
        $this->allowAllInGroup($crud_models, 'Location Manager');
        $this->allowAllInGroup($crud_models->forget('lead'), 'Regional Admin');
        $this->allowAllInGroup('leads', 'Sales Rep');
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
}
