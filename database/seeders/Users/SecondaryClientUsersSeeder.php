<?php

namespace Database\Seeders\Users;

use App\Actions\Fortify\CreateUser;
use App\Models\Clients\Client;
use App\Models\Clients\Security\SecurityRole;
use App\Models\Team;
use App\Models\User;
use App\Models\UserDetails;
use Database\Seeders\Users\UserSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Silber\Bouncer\Database\Role;
use Symfony\Component\VarDumper\VarDumper;

class SecondaryClientUsersSeeder extends UserSeeder
{
    protected $type = 'client-employees';

    protected function getUsersToAdd() : array
    {
        return [
            // The Kalamazoo
            [
                'first_name' => 'Mr',
                'last_name' => 'Owl',
                'email' => 'owl@kalamazoo.com',
                'role' => 'Location Manager',
                'client' => 'The Kalamazoo',
                'team_names' => ['The Kalamazoo Home Office', 'Zoo Sales Team', 'The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'SK',
                'last_name' => 'Tiger',
                'email' => 'kahn@kalamazoo.com',
                'role' => 'Regional Admin',
                'client' => 'The Kalamazoo',
                'team_names' => ['Zoo Sales Team','The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Baloo',
                'last_name' => 'Bear',
                'email' => 'baloo@kalamazoo.com',
                'role' => 'Sales Rep',
                'client' => 'The Kalamazoo',
                'team_names' => ['Zoo Sales Team','The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Louie',
                'last_name' => 'Orang',
                'email' => 'louie@kalamazoo.com',
                'role' => 'Sales Rep',
                'client' => 'The Kalamazoo',
                'team_names' => ['Zoo Sales Team','The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Mow',
                'last_name' => 'Gli',
                'email' => 'boy@kalamazoo.com',
                'role' => 'Employee',
                'client' => 'The Kalamazoo',
                'team_names' => ['Zoo Sales Team', 'The Kalamazoo Gym #1'],
            ],

            // Bodies By Brett
            // FitnessTruth
            // The Z
            // TruFit Athletic Clubs
            //Stencils
            //SciFi Purple Gyms

            // iFit
            [
                'first_name' => 'Bob',
                'last_name' => 'Kirk',
                'email' => 'bkirk@iFit.com',
                'role' => 'Regional Admin',
                'client' => 'iFit',
                'team_names' => [
                    'iFit Home Office', 'iFit Virginia',
                    'VA - Va Beach 1', 'VA - Va Beach 2'
                ],
            ],
            [
                'first_name' => 'Kirk',
                'last_name' => 'Roberts',
                'email' => 'kroberts@iFit.com',
                'role' => 'Regional Admin',
                'client' => 'iFit',
                'team_names' => [
                    'iFit Home Office', 'iFit Georgia',
                    'iFit Florida', 'FL - Tampa 1',
                    'FL - Lake City', 'FL - Hilliard',
                    'FL - Orange Park', 'FL - Tampa 2',
                    'GA - Atlanta 1', 'GA - Atlanta 2',
                    'GA - Atlanta 16'
                ],
            ],
            [
                'first_name' => 'Stan',
                'last_name' => 'Jacobs',
                'email' => 'owl@iFit.com',
                'role' => 'Location Manager',
                'client' => 'iFit',
                'team_names' => ['iFit Virginia', 'iFit Sales Team', 'VA - Va Beach 1', 'VA - Va Beach 2'],
            ],
            [
                'first_name' => 'Abbi',
                'last_name' => 'Abbington',
                'email' => 'aabing@iFit.com',
                'role' => 'Location Manager',
                'client' => 'iFit',
                'team_names' => ['iFit Florida', 'iFit Sales Team','FL - Tampa 2', 'iFit Sales Team Florida'],
            ],
            [
                'first_name' => 'Mark',
                'last_name' => 'Roughy',
                'email' => 'mroughy@iFit.com',
                'role' => 'Location Manager',
                'client' => 'iFit',
                'team_names' => ['iFit Georgia','iFit Sales Team','GA - Atlanta 1','iFit Sales Team Georgia/VA'],
            ],
            [
                'first_name' => 'Jessica',
                'last_name' => 'Hornsby',
                'email' => 'jhornsby@iFit.com',
                'role' => 'Sales Rep',
                'client' => 'iFit',
                'team_names' => ['iFit Sales Team', 'GA - Atlanta 16','iFit Sales Team Georgia/VA'],
            ],
            [
                'first_name' => 'Marco',
                'last_name' => 'Lopez',
                'email' => 'mlopez@iFit.com',
                'role' => 'Sales Rep',
                'client' => 'iFit',
                'team_names' => ['iFit Sales Team', 'FL - Tampa 2','iFit Sales Team Florida'],
            ],
        ];
    }

    protected function addUser(array $user)
    {
        $client = Client::whereName($user['client'])->first();

        $teams = Team::with('locations')->whereIn('name', $user['team_names'])->get();
        $team_ids = $teams->pluck('id');

        $possible_home_clubs = $teams->pluck('locations')->flatten()->keyBy('value')->values()->pluck('value');

        $home_club = $possible_home_clubs[random_int(0, $possible_home_clubs->count() -1 )];

        CreateUser::run([
            'client_id' => $client->id,
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'password' => 'Hello123!',
            'team_ids' => $team_ids,
            'role' => $user['role'],
            'home_club' => $home_club
        ]);
    }
}
