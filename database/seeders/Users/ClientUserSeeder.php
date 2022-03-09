<?php

namespace Database\Seeders\Users;


use App\Actions\Fortify\CreateUser;
use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Team;
use Illuminate\Support\Facades\Artisan;

class ClientUserSeeder extends UserSeeder
{
    protected $type = 'client';

    protected function getUsersToAdd() : array
    {
        return [
            [
                'first_name' => 'Mr',
                'last_name' => 'Giraffe',
                'email' => 'giraffe@kalamazoo.com',
                'role' => 'Account Owner',
                'client' => 'The Kalamazoo'
            ],
            [
                'first_name' => 'Malaki',
                'last_name' => 'Haualaki',
                'email' => 'malaki@thezclubs.com',
                'role' => 'Account Owner',
                'client' => 'The Z',
            ],
            [
                'first_name' => 'Bobby',
                'last_name' => 'Monyahan',
                'email' => 'monyahanb@clubtruth.com',
                'role' => 'Account Owner',
                'client' => 'FitnessTruth',
            ],
            [
                'first_name' => 'Sheri',
                'last_name' => 'Oteri',
                'email' => 'sherri@ifit.com',
                'role' => 'Account Owner',
                'client' => 'iFit',
            ],
            [
                'first_name' => 'Brett',
                'last_name' => 'Milam',
                'email' => 'brett+bbb@capeandbay.com',
                'role' => 'Account Owner',
                'client' => 'Bodies By Brett',
            ],
            [
                'first_name' => 'Arga',
                'last_name' => 'Barkbark',
                'email' => 'agabla@scifipurplegyms.com',
                'role' => 'Account Owner',
                'client' => 'Sci-Fi Purple Gyms',
            ],
            [
                'first_name' => 'Beth',
                'last_name' => 'Smith',
                'email' => 'bsmith@stencils.net',
                'role' => 'Account Owner',
                'client' => 'Stencils',
            ],
        ];
    }

    protected function addUser(array $user)
    {
        $client = Client::whereName($user['client'])->first();
        $home_office_team = $client->default_team_name()->first();
        $home_club = Location::whereClientId($client->id)->first()->gymrevenue_id;

        $senior_managers = ['Regional Manager', 'Account Owner', 'Admin', 'Regional Manager'];
        $managers = ['Location Manager'];

        $is_manager = in_array($user['role'], $senior_managers) ? 'Senior Manager' : (in_array($user['role'], $managers) ? 'Manager' : null);

        CreateUser::run([
            'client_id' => $client->id,
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'password' => 'Hello123!',
            'team_id' => $home_office_team->value,
//            'team_ids' => $team_ids,
            'role' => $user['role'],
            'home_club' => $home_club,
            'is_manager' => $is_manager
        ]);
    }
}
