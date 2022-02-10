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
                'teams' => ['The Kalamazoo Home Office', 'Zoo Sales Team'],
            ],
            [
                'name' => 'Baloo Bear',
                'email' => 'baloo@kalamazoo.com',
                'role' => 'Sales Rep',
                'client' => 'The Kalamazoo',
                'teams' => ['Zoo Sales Team'],
            ],
            [
                'name' => 'Louie Orang',
                'email' => 'louie@kalamazoo.com',
                'role' => 'Sales Rep',
                'client' => 'The Kalamazoo',
                'teams' => ['Zoo Sales Team'],
            ],

            [
                'name' => 'Bob Kirk',
                'email' => 'bkirk@iFit.com',
                'role' => 'Regional Admin',
                'client' => 'iFit',
                'teams' => ['iFit Home Office', 'iFit Virginia'],
            ],
            [
                'name' => 'Kirk Roberts',
                'email' => 'kroberts@iFit.com',
                'role' => 'Regional Admin',
                'client' => 'iFit',
                'teams' => ['iFit Home Office', 'iFit Georgia', 'iFit Florida'],
            ],
            [
                'name' => 'Stan Jacobs',
                'email' => 'owl@iFit.com',
                'role' => 'Location Manager',
                'client' => 'iFit',
                'teams' => ['iFit Virginia', 'iFit Sales Team'],
            ],
            [
                'name' => 'Abbi Abbington',
                'email' => 'aabing@iFit.com',
                'role' => 'Location Manager',
                'client' => 'iFit',
                'teams' => ['iFit Florida', 'iFit Sales Team'],
            ],
            [
                'name' => 'Mark Roughy',
                'email' => 'mroughy@iFit.com',
                'role' => 'Location Manager',
                'client' => 'iFit',
                'teams' => ['iFit Georgia','iFit Sales Team'],
            ],
            [
                'name' => 'Jessica Hornsby',
                'email' => 'jhornsby@iFit.com',
                'role' => 'Sales Rep',
                'client' => 'iFit',
                'teams' => ['iFit Sales Team'],
            ],
            [
                'name' => 'Marco Lopez',
                'email' => 'mlopez@iFit.com',
                'role' => 'Sales Rep',
                'client' => 'iFit',
                'teams' => ['iFit Sales Team'],
            ],
        ];
    }

    protected function addUser(array $user)
    {
        $client = Client::whereName($user['client'])->first();

        $teams = Team::whereIn('name', $user['teams'])->get(['id'])->pluck('id');

        CreateUser::run([
            'client_id' => $client->id,
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => 'Hello123!',
            'teams' => $teams,
            'role' => $user['role']
        ]);
        return;


        // Create User
        $new_user = User::create([
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => bcrypt('Hello123!')
        ]);

        // Associate with the Client
        UserDetails::create([
            'user_id' => $new_user->id,
            'name' => 'associated_client',
            'value' => $client->id,
            'active' => 1,
        ]);

        //add security role
        $role = Role::whereName($user['role'])->first();
        if ($role) {
            $security_role = SecurityRole::whereClientId($client->id)->whereRoleId($role->id)->first();//get default security role for role if exists
            if ($security_role) {
                UserDetails::create([
                    'user_id' => $new_user->id,
                    'name' => 'security_role',
                    'value' => $security_role->id
                ]);
            }
        }

        foreach($user['teams'] as $idx => $team_name)
        {
            $team_model = Team::whereName($team_name)->first();

            if($idx == 0)
            {
                // Set default team for User with 1st record
                UserDetails::create([
                    'user_id' => $new_user->id,
                    'name' => 'default_team',
                    'value' => $team_model->id,
                    'active' => 1
                ]);
            }

            // set team_user record to $client's default-team's team_id (or use an action if possible)
            $team_model->users()->attach(
                $new_user, ['role' => $user['role']]
            );

        }

        // Use Bouncer to assign the Admin Role
        Bouncer::assign($user['role'])->to($new_user);

        VarDumper::dump('Created! Checking out...');
    }
}
