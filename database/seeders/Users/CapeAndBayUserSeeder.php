<?php

namespace Database\Seeders\Users;

use App\Models\Team;
use Illuminate\Support\Facades\Artisan;

class CapeAndBayUserSeeder extends UserSeeder
{
    protected $type = 'cnb';

    protected function getUsersToAdd() : array
    {
        Team::firstOrCreate([
            'user_id' => 1,
            'name' => 'Cape & Bay Admin Team',
            'personal_team' => 0
        ]);

        return [
            [
                'name' => 'Angel Gonzalez',
                'email' => 'angel@capeandbay.com',
            ],
            [
                'name' => 'Tareq Othman',
                'email' => 'tareq@capeandbay.com',
            ],
            [
                'name' => 'Zaid Dabus',
                'email' => 'zaid@capeandbay.com',
            ],
            [
                'name' => 'Brett Milam',
                'email' => 'brett@capeandbay.com',
            ],
            [
                'name' => 'Ammar Zalatino',
                'email' => 'ammar@capeandbay.com',
            ],
            [
                'name' => 'Philip Krugel',
                'email' => 'philip@capeandbay.com',
            ],
            [
                'name' => 'Sterling Webb',
                'email' => 'sterling@capeandbay.com',
            ],
            [
                'name' => 'Shivam Shewa',
                'email' => 'shivam@capeandbay.com',
            ],
            [
                'name' => 'Alec Salzman',
                'email' => 'alec@gymrevenue.com',
            ],
            [
                'name' => 'Sami ',
                'email' => 'sami@gymrevenue.com',
            ],
			[
                'name' => 'Steve ',
                'email' => 'steve@capeandbay.com',
            ],
            [
                'name' => 'Blair Patterson',
                'email' => 'blair@capeandbay.com',
            ],
        ];
    }

    protected function addUser(array $user)
    {
        Artisan::call("user:create --name=\"{$user['name']}\" --email={$user['email']} --client=0 --role=Admin");
    }
}
