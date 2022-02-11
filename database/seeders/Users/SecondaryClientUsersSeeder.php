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
            [
                'name' => 'Mr Owl',
                'email' => 'owl@kalamazoo.com',
                'role' => 'Location Manager',
                'client' => 'The Kalamazoo',
                'team_ids' => ['The Kalamazoo Home Office', 'Zoo Sales Team'],
            ],
            [
                'name' => 'Baloo Bear',
                'email' => 'baloo@kalamazoo.com',
                'role' => 'Sales Rep',
                'client' => 'The Kalamazoo',
                'team_ids' => ['Zoo Sales Team'],
            ],
            [
                'name' => 'Louie Orang',
                'email' => 'louie@kalamazoo.com',
                'role' => 'Sales Rep',
                'client' => 'The Kalamazoo',
                'team_ids' => ['Zoo Sales Team'],
            ],

            [
                'name' => 'Bob Kirk',
                'email' => 'bkirk@iFit.com',
                'role' => 'Regional Admin',
                'client' => 'iFit',
                'team_ids' => ['iFit Home Office', 'iFit Virginia'],
            ],
            [
                'name' => 'Kirk Roberts',
                'email' => 'kroberts@iFit.com',
                'role' => 'Regional Admin',
                'client' => 'iFit',
                'team_ids' => ['iFit Home Office', 'iFit Georgia', 'iFit Florida'],
            ],
            [
                'name' => 'Stan Jacobs',
                'email' => 'owl@iFit.com',
                'role' => 'Location Manager',
                'client' => 'iFit',
                'team_ids' => ['iFit Virginia', 'iFit Sales Team'],
            ],
            [
                'name' => 'Abbi Abbington',
                'email' => 'aabing@iFit.com',
                'role' => 'Location Manager',
                'client' => 'iFit',
                'team_ids' => ['iFit Florida', 'iFit Sales Team'],
            ],
            [
                'name' => 'Mark Roughy',
                'email' => 'mroughy@iFit.com',
                'role' => 'Location Manager',
                'client' => 'iFit',
                'team_ids' => ['iFit Georgia','iFit Sales Team'],
            ],
            [
                'name' => 'Jessica Hornsby',
                'email' => 'jhornsby@iFit.com',
                'role' => 'Sales Rep',
                'client' => 'iFit',
                'team_ids' => ['iFit Sales Team'],
            ],
            [
                'name' => 'Marco Lopez',
                'email' => 'mlopez@iFit.com',
                'role' => 'Sales Rep',
                'client' => 'iFit',
                'team_ids' => ['iFit Sales Team'],
            ],
        ];
    }

    protected function addUser(array $user)
    {
        $client = Client::whereName($user['client'])->first();

        $team_ids = Team::whereIn('name', $user['team_ids'])->get(['id'])->pluck('id');

        CreateUser::run([
            'client_id' => $client->id,
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => 'Hello123!',
            'team_ids' => $team_ids,
            'role' => $user['role']
        ]);
    }
}
