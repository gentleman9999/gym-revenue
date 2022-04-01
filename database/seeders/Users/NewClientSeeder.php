<?php

namespace Database\Seeders\Users;

use App\Actions\Fortify\CreateUser;
use App\Models\Clients\Classification;
use App\Models\Clients\Client;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Silber\Bouncer\Database\Role;
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
            $roles = Role::whereScope($client->id)->get();
            $classification = Classification::whereClientId($client->id)->get();

            /** Collect all teams into an array that so everyone is on every team */
            $team_ids = $client->teams()->pluck('value');
            $teams = Team::whereIn('id', $team_ids)->get();
            $team_names = [];
            foreach ($teams as $team)
            {
                $team_names[] = $team['name'];
            }


            foreach ($roles as $role)
            {
                if($role['title'] !== 'Employee') {
                    if ($role['title'] === 'Sales Rep') {
                        $users = User::factory()
                            ->count(5)
                            ->make([
                                'client' => $client->name,
                                'role' => $role['id'],
                                'team_names' => $team_names
                            ]);
                    } else {
                        $users = User::factory()
                            ->count(1)
                            ->make([
                                'client' => $client->name,
                                'role' => $role['id'],
                                'team_names' => $team_names
                            ]);
                    }

                    foreach($users as $user)
                    {
                        $client = Client::whereName($user['client'])->first();
                        $teams = Team::with('locations')->whereIn('name', $user['team_names'])->get();
                        $team_ids = $teams->pluck('id');
                        $possible_home_clubs = $teams->pluck('locations')->flatten()->keyBy('value')->values()->pluck('value');
                        if($possible_home_clubs->count() > 0 )
                            $home_club = $possible_home_clubs[random_int(0, $possible_home_clubs->count() - 1)];
                        else
                            $home_club = null;
                        $senior_managers = [3, 2, 1];
                        $managers = [4];
                        $manager = in_array($user['role'], $senior_managers) ? 'Senior Manager' : (in_array($user['role'], $managers) ? 'Manager' : null);
                        CreateUser::run([
                            'client_id' => $client->id,
                            'first_name' => $user['first_name'],
                            'last_name' => $user['last_name'],
                            'email' => $user['email'],
                            'password' => 'Hello123!',
                            'team_ids' => $team_ids,
                            'role_id' => $user['role'],
                            'home_club' => $home_club,
                            'is_manager' => $manager
                        ]);
                    }
                } else {
                    foreach ($classification as $class)
                    {
                        if($class['title'] == 'Club Associate') {
                            $users = User::factory()
                                ->count(10)
                                ->make([
                                    'client' => $client->name,
                                    'role' => $role['id'],
                                    'classification' => $class['id'],
                                    'team_names' => $team_names
                                ]);
                        } else if($class['title'] == 'Fitness Trainer' || $class['title'] == 'Personal Trainer') {
                            $users = User::factory()
                                ->count(5)
                                ->make([
                                    'client' => $client->name,
                                    'role' => $role['id'],
                                    'classification' => $class['id'],
                                    'team_names' => $team_names
                                ]);
                        } else {
                            $users = User::factory()
                                ->count(2)
                                ->make([
                                    'client' => $client->name,
                                    'role' => $role['id'],
                                    'classification' => $class['id'],
                                    'team_names' => $team_names
                                ]);
                        }

                        foreach ($users as $user) {
                            $client = Client::whereName($user['client'])->first();
                            $teams = Team::with('locations')->whereIn('name', $user['team_names'])->get();
                            $team_ids = $teams->pluck('id');
                            $possible_home_clubs = $teams->pluck('locations')->flatten()->keyBy('value')->values()->pluck('value');
                            if ($possible_home_clubs->count() > 0)
                                $home_club = $possible_home_clubs[random_int(0, $possible_home_clubs->count() - 1)];
                            else
                                $home_club = null;

                            CreateUser::run([
                                'client_id' => $client->id,
                                'first_name' => $user['first_name'],
                                'last_name' => $user['last_name'],
                                'email' => $user['email'],
                                'password' => 'Hello123!',
                                'team_ids' => $team_ids,
                                'role_id' => $user['role'],
                                'classification' => $user['classification'],
                                'home_club' => $home_club,
                                'is_manager' => null,
                            ]);
                        }
                    }
                }
            }

        }

    }
}
