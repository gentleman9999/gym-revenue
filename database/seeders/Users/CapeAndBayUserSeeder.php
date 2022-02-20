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
            'personal_team' => 0,
            'default_team' => 1
        ]);

        return [
            [
                'first_name' => 'Angel',
                'last_name' => 'Gonzalez',
                'email' => 'angel@capeandbay.com',
            ],
            [
                'first_name' => 'Tareq',
                'last_name' => 'Othman',
                'email' => 'tareq@capeandbay.com',
            ],
            [
                'first_name' => 'Zaid',
                'last_name' => 'Dabus',
                'email' => 'zaid@capeandbay.com',
            ],
            [
                'first_name' => 'Brett',
                'last_name' => 'Milam',
                'email' => 'brett@capeandbay.com',
            ],
            [
                'first_name' => 'Ammar',
                'last_name' => 'Zalatino',
                'email' => 'ammar@capeandbay.com',
            ],
            [
                'first_name' => 'Philip',
                'last_name' => 'Krogel',
                'email' => 'philip@capeandbay.com',
            ],
            [
                'first_name' => 'Sterling',
                'last_name' => 'Webb',
                'email' => 'sterling@capeandbay.com',
            ],
            [
                'first_name' => 'Shivam',
                'last_name' => 'Shewa',
                'email' => 'shivam@capeandbay.com',
            ],
            [
                'first_name' => 'Alec',
                'last_name' => 'Salzman',
                'email' => 'alec@gymrevenue.com',
            ],
            [
                'first_name' => 'Sami',
                'last_name' => 'S',
                'email' => 'sami@gymrevenue.com',
            ],
			[
                'first_name' => 'Steve',
                'last_name' => 'Deemer',
                'email' => 'steve@capeandbay.com',
            ],
            [
                'first_name' => 'Blair',
                'last_name' => 'Patterson',
                'email' => 'blair@capeandbay.com',
            ],
        ];
    }

    protected function addUser(array $user)
    {
        Artisan::call("user:create --firstname=\"{$user['first_name']}\" --lastname=\"{$user['last_name']}\" --email={$user['email']} --client=0 --role=Admin");
    }
}
