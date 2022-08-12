<?php

namespace Database\Seeders\Users;

use App\Domain\Teams\Actions\CreateTeam;
use App\Domain\Users\Actions\CreateUser;

class CapeAndBayUserSeeder extends UserSeeder
{
    protected $type = 'cnb';
    protected $cnb_team = null;

    protected function getUsersToAdd(): array
    {
        $this->cnb_team = CreateTeam::run([
            'client_id' => null,
            'name' => 'Cape & Bay Admin Team',
            'home_team' => 1,
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
                'last_name' => 'Carroll',
                'email' => 'sami@gymrevenue.com',
            ],
            [
                'first_name' => 'Ron',
                'last_name' => 'Merryman',
                'email' => 'ron@gymrevenue.com',
            ],
            [
                'first_name' => 'Tommy',
                'last_name' => 'Lee',
                'email' => 'tommy@capeandbay.com',
            ],
            [
                'first_name' => 'Blair',
                'last_name' => 'Patterson',
                'email' => 'blair@capeandbay.com',
            ],
            [
                'first_name' => 'Adam',
                'last_name' => 'Vandall',
                'email' => 'adam@capeandbay.com',
            ],
            [
                'first_name' => 'Chris',
                'last_name' => 'Italiano',
                'email' => 'chris@capeandbay.com',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Duncan',
                'email' => 'david@capeandbay.com',
            ],
            [
                'first_name' => 'Larry',
                'last_name' => 'Herrod',
                'email' => 'larry@capeandbay.com',
            ],
            [
                'first_name' => 'Jen',
                'last_name' => 'Jen',
                'email' => 'jen@gymrevenue.com',
            ],
            [
                'first_name' => 'Chrys',
                'last_name' => 'Ugwu',
                'email' => 'chrys@capeandbay.com',
            ],
        ];
    }

    protected function addUser(array $user)
    {
        CreateUser::run([
            'client_id' => null,
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'email' => $user['email'],
            'password' => 'Hello123!',
            'team_id' => $this->cnb_team->id,
            'role_id' => 1,
//            'home_location_id' => $home_location_id,
            'manager' => 'Senior Manager',
        ]);
    }
}
