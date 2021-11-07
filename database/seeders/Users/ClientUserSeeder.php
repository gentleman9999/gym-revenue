<?php

namespace Database\Seeders\Users;


use App\Models\Clients\Client;
use Illuminate\Support\Facades\Artisan;

class ClientUserSeeder extends UserSeeder
{
    protected $type = 'client';

    protected function getUsersToAdd() : array
    {
        return [
            [
                'name' => 'Mr Giraffe',
                'email' => 'giraffe@kalamazoo.com',
                'role' => 'Account Owner',
                'client' => 'The Kalamazoo'
            ],
            [
                'name' => 'Malaki Haualaki',
                'email' => 'malaki@thezclubs.com',
                'role' => 'Account Owner',
                'client' => 'The Z',
            ],
            [
                'name' => 'Bobby Monyahan',
                'email' => 'monyahanb@clubtruth.com',
                'role' => 'Account Owner',
                'client' => 'FitnessTruth',
            ],
            [
                'name' => 'Sheri Oteri',
                'email' => 'sherri@ifit.com',
                'role' => 'Account Owner',
                'client' => 'iFit',
            ],
            [
                'name' => 'Brett Milam',
                'email' => 'brett+bbb@capeandbay.com',
                'role' => 'Account Owner',
                'client' => 'Bodies By Brett',
            ],
            [
                'name' => 'Arga Barkbark',
                'email' => 'agabla@scifipurplegyms.com',
                'role' => 'Account Owner',
                'client' => 'Sci-Fi Purple Gyms',
            ],
            [
                'name' => 'Beth Smith',
                'email' => 'bsmith@stencils.net',
                'role' => 'Account Owner',
                'client' => 'Stencils',
            ],
        ];
    }

    protected function addUser(array $user)
    {
        $client = Client::whereName($user['client'])->first();
        Artisan::call("user:new --name=\"{$user['name']}\" --email={$user['email']} --client={$client->id} --role=\"{$user['role']}\"");
    }
}
