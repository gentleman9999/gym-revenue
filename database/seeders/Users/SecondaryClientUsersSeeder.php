<?php

namespace Database\Seeders\Users;

use App\Actions\Fortify\CreateUser;
use App\Models\Clients\Client;
use App\Models\Team;

class SecondaryClientUsersSeeder extends UserSeeder
{
    protected $type = 'client-employees';

    protected function getUsersToAdd(): array
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
                'team_names' => ['Zoo Sales Team', 'The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Baloo',
                'last_name' => 'Bear',
                'email' => 'baloo@kalamazoo.com',
                'role' => 'Sales Rep',
                'client' => 'The Kalamazoo',
                'team_names' => ['Zoo Sales Team', 'The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Louie',
                'last_name' => 'Orang',
                'email' => 'louie@kalamazoo.com',
                'role' => 'Sales Rep',
                'client' => 'The Kalamazoo',
                'team_names' => ['Zoo Sales Team', 'The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Mow',
                'last_name' => 'Gli',
                'email' => 'boy@kalamazoo.com',
                'role' => 'Employee',
                'client' => 'The Kalamazoo',
                'team_names' => ['Zoo Sales Team', 'The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Ron',
                'last_name' => 'Swanson',
                'email' => 'rswanson@kalamazoo.com',
                'role' => 'Employee',
                'client' => 'The Kalamazoo',
                'team_names' => ['Zoo Sales Team', 'The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Ronda',
                'last_name' => 'McTonda',
                'email' => 'rmctonda@kalamazoo.com',
                'role' => 'Employee',
                'client' => 'The Kalamazoo',
                'team_names' => ['The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Jack',
                'last_name' => 'Bauer',
                'email' => 'jbauer@kalamazoo.com',
                'role' => 'Employee',
                'client' => 'The Kalamazoo',
                'team_names' => ['The Kalamazoo Gym #1', 'The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Ragnar',
                'last_name' => 'Lodbrok',
                'email' => 'jbauer@kalamazoo.com',
                'role' => 'Employee',
                'client' => 'The Kalamazoo',
                'team_names' => ['Zoo Sales Team', 'The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Satoshi',
                'last_name' => 'Nakamoto',
                'email' => 'jbauer@kalamazoo.com',
                'role' => 'Employee',
                'client' => 'The Kalamazoo',
                'team_names' => ['Zoo Sales Team', 'The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Neil',
                'last_name' => 'Peart',
                'email' => 'npeart@kalamazoo.com',
                'role' => 'Employee',
                'client' => 'The Kalamazoo',
                'team_names' => ['The Kalamazoo Gym #1'],
            ],
            [
                'first_name' => 'Ozzy',
                'last_name' => 'Osbourne',
                'email' => 'oosbourne@kalamazoo.com',
                'role' => 'Employee',
                'client' => 'The Kalamazoo',
                'team_names' => ['The Kalamazoo Gym #1'],
            ],

            // Bodies By Brett
            // FitnessTruth
            // The Z
            // TruFit Athletic Clubs
            //Stencils
            [
                'first_name' => 'Michael',
                'last_name' => 'Scott',
                'email' => 'mscott@stencils.net',
                'role' => 'Location Manager',
                'client' => 'Stencils',
                'team_names' => ['Stencils Portland'],
            ],
            [
                'first_name' => 'Creed',
                'last_name' => 'Bratton',
                'email' => 'cbratton@stencils.net',
                'role' => 'Employee',
                'client' => 'Stencils',
                'team_names' => ['Stencils Portland'],
            ],
            [
                'first_name' => 'Kevin',
                'last_name' => 'Malone',
                'email' => 'kmalone@stencils.net',
                'role' => 'Employee',
                'client' => 'Stencils',
                'team_names' => ['Stencils Portland'],
            ],
            [
                'first_name' => 'Stanley',
                'last_name' => 'Hudson',
                'email' => 'shudson@stencils.net',
                'role' => 'Employee',
                'client' => 'Stencils',
                'team_names' => ['Stencils Portland'],
            ],
            [
                'first_name' => 'Angela',
                'last_name' => 'Martin',
                'email' => 'jhalpert@stencils.net',
                'role' => 'Employee',
                'client' => 'Stencils',
                'team_names' => ['Stencils Portland'],
            ],
            [
                'first_name' => 'Jim',
                'last_name' => 'Halpert',
                'email' => 'jhalpert@stencils.net',
                'role' => 'Sales Rep',
                'client' => 'Stencils',
                'team_names' => ['Stencils Portland'],
            ],
            [
                'first_name' => 'Dwight',
                'last_name' => 'Bratton',
                'email' => 'cbratton@stencils.net',
                'role' => 'Sales Rep',
                'client' => 'Stencils',
                'team_names' => ['Stencils Portland'],
            ],
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
                    'VA - Va Beach 1', 'VA - Va Beach 2',
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
                    'GA - Atlanta 16',
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
                'team_names' => ['iFit Florida', 'iFit Sales Team', 'FL - Tampa 2', 'iFit Sales Team Florida'],
            ],
            [
                'first_name' => 'Mark',
                'last_name' => 'Roughy',
                'email' => 'mroughy@iFit.com',
                'role' => 'Location Manager',
                'client' => 'iFit',
                'team_names' => ['iFit Georgia', 'iFit Sales Team', 'GA - Atlanta 1', 'iFit Sales Team Georgia/VA'],
            ],
            [
                'first_name' => 'Jessica',
                'last_name' => 'Hornsby',
                'email' => 'jhornsby@iFit.com',
                'role' => 'Sales Rep',
                'client' => 'iFit',
                'team_names' => ['iFit Sales Team', 'GA - Atlanta 16', 'iFit Sales Team Georgia/VA'],
            ],
            [
                'first_name' => 'Marco',
                'last_name' => 'Lopez',
                'email' => 'mlopez@iFit.com',
                'role' => 'Sales Rep',
                'client' => 'iFit',
                'team_names' => ['iFit Sales Team', 'FL - Tampa 2', 'iFit Sales Team Florida'],
            ],
            [
                'first_name' => 'Bill',
                'last_name' => 'Nye',
                'email' => 'bnye@iFit.com',
                'role' => 'Employee',
                'client' => 'iFit',
                'team_names' => ['iFit Florida'],
            ],
            [
                'first_name' => 'Marilyn',
                'last_name' => 'Monroe',
                'email' => 'mmonroe@iFit.com',
                'role' => 'Employee',
                'client' => 'iFit',
                'team_names' => ['iFit Florida'],
            ],
            [
                'first_name' => 'Jill',
                'last_name' => 'McClaus',
                'email' => 'bnye@iFit.com',
                'role' => 'Employee',
                'client' => 'iFit',
                'team_names' => ['iFit Georgia'],
            ],
            [
                'first_name' => 'Kim',
                'last_name' => 'Kardashian',
                'email' => 'kkardashian@iFit.com',
                'role' => 'Employee',
                'client' => 'iFit',
                'team_names' => ['iFit Georgia'],
            ],
            [
                'first_name' => 'Peter',
                'last_name' => 'Parker',
                'email' => 'pparker@iFit.com',
                'role' => 'Employee',
                'client' => 'iFit',
                'team_names' => ['iFit Virginia'],
            ],
            [
                'first_name' => 'Warren',
                'last_name' => 'Buffett',
                'email' => 'wbuffett@iFit.com',
                'role' => 'Employee',
                'client' => 'iFit',
                'team_names' => ['iFit Virginia'],
            ],
            [
                'first_name' => 'Jimmy',
                'last_name' => 'Buffett',
                'email' => 'jbuffett@iFit.com',
                'role' => 'Employee',
                'client' => 'iFit',
                'team_names' => ['iFit Virginia', 'iFit Georgia'],
            ],
        ];
    }

    protected function addUser(array $user)
    {
        $client = Client::whereName($user['client'])->first();

        $teams = Team::with('locations')->whereIn('name', $user['team_names'])->get();
        $team_ids = $teams->pluck('id');

        $possible_home_clubs = $teams->pluck('locations')->flatten()->keyBy('value')->values()->pluck('value');
        $home_club = $possible_home_clubs[random_int(0, $possible_home_clubs->count() - 1)];

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
            'is_manager' => $manager,
        ]);
    }
}
