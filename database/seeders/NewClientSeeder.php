<?php

namespace Database\Seeders;

use App\Actions\Fortify\CreateUser;
use App\Models\Clients\Client;
use App\Models\Clients\Security\SecurityRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class NewClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VarDumper::dump("Adding Factory Users...");

        $clients = Client::all();
        foreach ($clients as $client) {
            $client = Client::with('teams')->find($client->id);
            VarDumper::dump("Adding ".$client->name." Users...");

            /** Find all teams for client and put the names in an array */
            $securityRoles = SecurityRole::whereClientId($client->id)->get();
            $team_ids = $client->teams()->pluck('value');
            $teams = Team::whereIn('id', $team_ids)->get();
            $team_names = [];
            foreach ($teams as $team) {
                $team_names[] = $team['name'];
            }

            foreach ($securityRoles as $role)
            {

                if ($role->security_role === 'Sales Rep') {
                    $users = User::factory()
                        ->count(5)
                        ->make([
                            'client' => $client->name,
                            'role' => 'Sales Rep',
                            'team_names' => $team_names
                        ]);
                } else if ($role->security_role === 'Club Associate') {
                    $users = User::factory()
                        ->count(10)
                        ->make([
                            'client' => $client->name,
                            'role' => $role->security_role,
                            'team_names' => $team_names
                        ]);
                } else if ($role->security_role === 'Floor Manager/Team Lead') {
                    $users = User::factory()
                        ->count(2)
                        ->make([
                            'client' => $client->name,
                            'role' => $role->security_role,
                            'team_names' => $team_names
                        ]);
                } else if ($role->security_role === 'Fitness Trainer') {
                    $users = User::factory()
                        ->count(5)
                        ->make([
                            'client' => $client->name,
                            'role' => $role->security_role,
                            'team_names' => $team_names
                        ]);
                } else if ($role->security_role === 'Personal Trainer') {
                    $users = User::factory()
                        ->count(5)
                        ->make([
                            'client' => $client->name,
                            'role' => $role->security_role,
                            'team_names' => $team_names
                        ]);
                } else if ($role->security_role === 'Instructor') {
                    $users = User::factory()
                        ->count(2)
                        ->make([
                            'client' => $client->name,
                            'role' => $role->security_role,
                            'team_names' => $team_names
                        ]);
                } else if ($role->security_role === 'Sanitation') {
                    $users = User::factory()
                        ->count(2)
                        ->make([
                            'client' => $client->name,
                            'role' => $role->security_role,
                            'team_names' => $team_names
                        ]);
                } else {
                    $users = User::factory()
                        ->count(1)
                        ->make([
                            'client' => $client->name,
                            'role' => $role->security_role,
                            'team_names' => $team_names
                        ]);
                }

                foreach($users as $user) {

                    $client = Client::whereName($user['client'])->first();

                    $teams = Team::with('locations')->whereIn('name', $user['team_names'])->get();
                    $team_ids = $teams->pluck('id');

                    $possible_home_clubs = $teams->pluck('locations')->flatten()->keyBy('value')->values()->pluck('value');

                    if($possible_home_clubs->count() > 0 )
                        $home_club = $possible_home_clubs[random_int(0, $possible_home_clubs->count() - 1)];
                    else
                        $home_club = null;

                    $senior_managers = ['Regional Manager', 'Account Owner', 'Admin', 'Regional Manager'];
                    $managers = ['Location Manager'];

                    $manager = in_array($user['role'], $senior_managers) ? 'Senior Manager' : (in_array($user['role'], $managers) ? 'Manager' : null);

                    CreateUser::run([
                        'client_id' => $client->id,
                        'first_name' => $user['first_name'],
                        'last_name' => $user['last_name'],
                        'email' => $user['email'],
                        'password' => 'Hello123!',
                        'team_ids' => $team_ids,
                        'role' => $user['role'],
                        'home_club' => $home_club,
                        'is_manager' => $manager
                    ]);
                }

            }

        }

    }
}
