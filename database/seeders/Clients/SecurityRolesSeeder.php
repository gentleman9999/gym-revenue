<?php

namespace Database\Seeders\Clients;

use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Clients\Classification;
use App\Models\Endusers\Lead;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Database\Role;
use Symfony\Component\VarDumper\VarDumper;

class SecurityRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $clients = Client::all();
        $roles = Role::all()->keyBy('name');
        $security_roles = [
            [
                'security_role' => 'Account Owner',
                'role' => 'Account Owner',
            ],
            [
                'security_role' => 'Location Manager - GM',
                'role' => 'Location Manager',
            ],
            [
                'security_role' => 'Region Admin - District Mgr',
                'role' => 'Regional Admin',
            ],
            [
                'security_role' => 'Sales Rep - InStore Sales',
                'role' => 'Sales Rep',
            ],
            [
                'security_role' => 'Club Associate',
                'role' => 'Employee',
            ],
            [
                'security_role' => 'Floor Manager/Team Lead',
                'role' => 'Employee',
            ],
            [
                'security_role' => 'Fitness Trainer',
                'role' => 'Fitness Trainer',
            ],
            [
                'security_role' => 'Personal Trainer',
                'role' => 'Personal Trainer',
            ],
            [
                'security_role' => 'Instructor',
                'role' => 'Instructor',
            ],
            [
                'security_role' => 'Fitness Manager',
                'role' => 'Fitness Manager',
            ],
            [
                'security_role' => 'Sanitation',
                'role' => 'Sanitation',
            ],
            [
                'security_role' => 'Human Resources',
                'role' => 'Human Resources',
            ],
            [
                'security_role' => 'Day Care Manager',
                'role' => 'Day Care Manager',
            ],
        ];
        $clients->each(function ($client) use ($security_roles, $roles) {
            foreach ($security_roles as $security_role) {
                VarDumper::dump("Creating Security Role '{$security_role['security_role']}' for Client - {$client->name}");
                // Get the Bounce Role Model for the Security Role's Linked role.
                $role = $roles[$security_role['role']];
                // Query for its abilities
                $abilities = $role->abilities()->get();
                // Get all of the client's teams
                $client_teams = $client->teams()->get();
                $client_clubs = $client->locations()->get();

                $ability_ids = [];
                // For each team
                foreach($client_teams as $team)
                {
                    $team_model = Team::find($team->value);
                    foreach ($abilities as $ability)
                    {
                        if($ability->entity_type == $team_model::class)
                        {
                            $ability_ids[] = [
                                'ability' => $ability->name,
                                'entity' => $team_model::class,
                                'entity_id' => $team_model->id
                            ];
                        }
                    }
                }

                // For each location
                foreach($client_clubs as $client_club)
                {
                    foreach ($abilities as $ability)
                    {
                        if($ability->entity_type == $client_club::class)
                        {
                            $ability_ids[] = [
                                'ability' => $ability->name,
                                'entity' => $team::class,
                                'entity_id' => $client_club->id
                            ];
                        }
                    }
                }

                // For the rest
                foreach ($abilities as $ability)
                {
                    if($ability->entity_type == Location::class)
                    {
                        $ability_ids[] = [
                            'ability' => $ability->name,
                            'entity' => Location::class,
                            'entity_id' => null
                        ];
                    }
                    else if($ability->entity_type == Lead::class)
                    {
                        $ability_ids[] = [
                            'ability' => $ability->name,
                            'entity' => Lead::class,
                            'entity_id' => null
                        ];
                    }
                    else if($ability->entity_type == User::class)
                    {
                        $ability_ids[] = [
                            'ability' => $ability->name,
                            'entity' => User::class,
                            'entity_id' => null
                        ];
                    }
                }

                $security_role_id = $roles[$security_role['role']]->name;


                Classification::create([
                    'client_id' => $client->id,
                    'role_id' => $roles[$security_role['role']]->id,
                    'security_role' => $security_role_id,
                    'ability_ids' => $ability_ids
                ]);
            }
        });
    }
}
