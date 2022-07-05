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
        $amountOfSalesReps = 5;
        $amountOfAssociates = 10;
        $amountOfTrainers = 5;
        $amountOfElse = 2;
        if (env('QUICK_SEED')) {
            $amountOfSalesReps = 1;
            $amountOfAssociates = 1;
            $amountOfTrainers = 1;
            $amountOfElse = 1;
        }
        VarDumper::dump("Adding Factory Users...");

        $clients = Client::all();
        foreach ($clients as $client) {
            $client = Client::with('teams')->find($client->id);
            VarDumper::dump("Adding ".$client->name." Users...");

            /** Find all teams for client and put the names in an array */
            $roles = Role::whereScope($client->id)->get();
            $classification = Classification::whereClientId($client->id)->get();

            /** Collect all teams into an array that so everyone is on every team */
            $team_ids = $client->teams()->pluck('id');
            $teams = Team::whereIn('id', $team_ids)->get();
            $team_names = [];
            foreach ($teams as $team) {
                $team_names[] = $team['name'];
            }


            foreach ($roles as $role) {
                if ($role['title'] !== 'Employee') {
                    if ($role['title'] === 'Sales Rep') {
                        $users = User::factory()
                            ->count($amountOfSalesReps)
                            ->make([
                                'client' => $client->name,
                                'role_id' => $role['id'],
                                'team_names' => $team_names,
                            ]);
                    } else {
                        $users = User::factory()
                            ->count(1)
                            ->make([
                                'client' => $client->name,
                                'role_id' => $role['id'],
                                'team_names' => $team_names,
                            ]);
                    }

                    foreach ($users as $user) {
                        $client = Client::whereName($user['client'])->first();
                        $teams = Team::with('locations')->whereIn('name', $user['team_names'])->get();
                        $team_ids = $teams->pluck('id');
                        $possible_home_locations = $teams->pluck('locations')->flatten()->keyBy('value')->values()->pluck('value');
                        if ($possible_home_locations->count() > 0) {
                            $home_location_id = $possible_home_locations[random_int(0, $possible_home_locations->count() - 1)];
                        } else {
                            $home_location_id = null;
                        }
                        $senior_managers = [3, 2, 1];
                        $managers = [4];
                        $manager = in_array($user['role_id'], $senior_managers) ? 'Senior Manager' : (in_array($user['role_id'], $managers) ? 'Manager' : null);
                        $final_data = array_merge($user->toArray(), [
                            'client_id' => $client->id,
                            'password' => 'Hello123!',
                            'team_ids' => $team_ids,
                            'home_location_id' => $home_location_id,
                            'manager' => $manager,
                        ]);
                        CreateUser::run($final_data);
                    }
                } else {
                    foreach ($classification as $class) {
                        if ($class['title'] == 'Club Associate') {
                            $users = User::factory()
                                ->count($amountOfAssociates)
                                ->make([
                                    'client' => $client->name,
                                    'role_id' => $role['id'],
                                    'team_names' => $team_names,
                                ]);
                        } elseif ($class['title'] == 'Fitness Trainer' || $class['title'] == 'Personal Trainer') {
                            $users = User::factory()
                                ->count($amountOfTrainers)
                                ->make([
                                    'client' => $client->name,
                                    'role_id' => $role['id'],
                                    'team_names' => $team_names,
                                ]);
                        } else {
                            $users = User::factory()
                                ->count($amountOfElse)
                                ->make([
                                    'client' => $client->name,
                                    'role_id' => $role['id'],
                                    'team_names' => $team_names,
                                ]);
                        }

                        foreach ($users as $user) {
                            $client = Client::whereName($user['client'])->first();
                            $teams = Team::with('locations')->whereIn('name', $user['team_names'])->get();
                            $team_ids = $teams->pluck('id');
                            $possible_home_locations = $teams->pluck('locations')->flatten()->keyBy('value')->values()->pluck('value');
                            if ($possible_home_locations->count() > 0) {
                                $home_location_id = $possible_home_locations[random_int(0, $possible_home_locations->count() - 1)];
                            } else {
                                $home_location_id = null;
                            }

                            $final_data = array_merge($user->toArray(), [
                                'client_id' => $client->id,
                                'password' => 'Hello123!',
                                'team_ids' => $team_ids,
                                'home_location_id' => $home_location_id,
                                'manager' => null,
                            ]);

                            CreateUser::run($final_data);
                        }
                    }
                }
            }
        }
    }
}
