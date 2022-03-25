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
        //Adding location owners
        return [
            [
                'first_name' => 'Mr',
                'last_name' => 'Giraffe',
                'email' => 'giraffe@kalamazoo.com',
                'client' => 'The Kalamazoo'
            ],
            [
                'first_name' => 'Malaki',
                'last_name' => 'Haualaki',
                'email' => 'malaki@thezclubs.com',
                'client' => 'The Z',
            ],
            [
                'first_name' => 'Bobby',
                'last_name' => 'Monyahan',
                'email' => 'monyahanb@clubtruth.com',
                'client' => 'FitnessTruth',
            ],
            [
                'first_name' => 'Sheri',
                'last_name' => 'Oteri',
                'email' => 'sherri@ifit.com',
                'client' => 'iFit',
            ],
            [
                'first_name' => 'Brett',
                'last_name' => 'Milam',
                'email' => 'brett+bbb@capeandbay.com',
                'client' => 'Bodies By Brett',
            ],
            [
                'first_name' => 'Arga',
                'last_name' => 'Barkbark',
                'email' => 'agabla@scifipurplegyms.com',
                'client' => 'Sci-Fi Purple Gyms',
            ],
            [
                'first_name' => 'Beth',
                'last_name' => 'Smith',
                'email' => 'bsmith@stencils.net',
                'client' => 'Stencils',
            ],
        ];
    }

    protected function addUser(array $user)
    {
        $client = Client::whereName($user['client'])->first();
        $home_office_team = $client->default_team_name()->first();
        $home_club = Location::whereClientId($client->id)->first()->gymrevenue_id;

        $is_manager = 'Senior Manager';

        CreateUser::run([
            'client_id' => $client->id,
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'password' => 'Hello123!',
            'team_id' => $home_office_team->value,
//            'team_ids' => $team_ids,
            'role' => 2, // account owner ID
            'home_club' => $home_club,
            'is_manager' => $is_manager
        ]);
    }
}
