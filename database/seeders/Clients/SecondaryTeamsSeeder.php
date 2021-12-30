<?php

namespace Database\Seeders\Clients;

use App\Models\Clients\ClientDetail;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class SecondaryTeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VarDumper::dump('Creating Cape & Bay Team(s)');

        $cnb_teams = [
            [
                'user_id' => 1,
                'name' => 'Cape & Bay Developers',
                'personal_team' => 0,
                'members' => [
                    'angel@capeandbay.com',
                    'shivam@capeandbay.com',
                    'philip@capeandbay.com',
                    'sterling@capeandbay.com',
                ]
            ],
        ];

        foreach ($cnb_teams as $team)
        {
            VarDumper::dump($team['name']);
            $members = $team['members'];
            unset($team['members']);
            $new_team = Team::firstOrCreate($team);

            if(count($members) > 0)
            {
                foreach($members as $idx => $email)
                {
                    $user = User::whereEmail($email)->first();
                    $new_team->users()->attach(
                        $user, ['role' => ($idx == 0) ? 'Admin' : 'Member']
                    );
                }
            }
        }

        $client_teams = [
            [
                'user_id' => 11,
                'name' => 'Zoo Sales Team',
                'personal_team' => 0,
            ],
            [
                'user_id' => 12,
                'name' => 'Big Island Team',
                'personal_team' => 0,
            ],
            [
                'user_id' => 12,
                'name' => 'Oahu Team',
                'personal_team' => 0,
            ],

            [
                'user_id' => 13,
                'name' => 'FitnessTruth Texas South',
                'personal_team' => 0,
            ],
            [
                'user_id' => 13,
                'name' => 'FitnessTruth Texas Houston',
                'personal_team' => 0,
            ],
            [
                'user_id' => 13,
                'name' => 'FitnessTruth Texas West',
                'personal_team' => 0,
            ],
            [
                'user_id' => 13,
                'name' => 'FitnessTruth Tennessee',
                'personal_team' => 0,
            ],

            [
                'user_id' => 14,
                'name' => 'iFit Florida',
                'personal_team' => 0,
            ],
            [
                'user_id' => 14,
                'name' => 'iFit Georgia',
                'personal_team' => 0,
            ],
            [
                'user_id' => 14,
                'name' => 'iFit Virginia',
                'personal_team' => 0,
            ],
            [
                'user_id' => 14,
                'name' => 'iFit Sales Team',
                'personal_team' => 0,
            ],

            [
                'user_id' => 15,
                'name' => 'Sales Bodies',
                'personal_team' => 0,
            ],

            [
                'user_id' => 17,
                'name' => 'Stencils Seattle',
                'personal_team' => 0,
            ],
            [
                'user_id' => 17,
                'name' => 'Stencils San Andreas',
                'personal_team' => 0,
            ],
            [
                'user_id' => 17,
                'name' => 'Stencils Portland',
                'personal_team' => 0,
            ],
        ];

        foreach ($client_teams as $team)
        {
            VarDumper::dump($team['name']);
            $new_team = Team::firstOrCreate($team);

            $user = User::whereId($team['user_id'])->with('associated_client')->first();
            $associated_client = $user->associated_client->value;
            ClientDetail::firstorCreate([
                'client_id' => $associated_client,
                'detail'=> 'team',
                'value' => $new_team->id,
                'active' => 1
            ]);
        }
    }
}
