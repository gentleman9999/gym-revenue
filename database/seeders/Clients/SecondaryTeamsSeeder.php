<?php

namespace Database\Seeders\Clients;

use App\Domain\Clients\Projections\Client;
use App\Domain\Teams\Actions\AddTeamMember;
use App\Domain\Teams\Actions\CreateTeam;
use App\Domain\Users\Models\User;
use Illuminate\Database\Seeder;

class SecondaryTeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        echo("Creating Cape & Bay Team(s)\n");

        $cnb_teams = [
            [
                'client_id' => null,
                'name'      => 'Cape & Bay Developers',
                'home_team' => 0,
                'members'   => [
                    'shivam@capeandbay.com',
                    'philip@capeandbay.com',
                    'sterling@capeandbay.com',
                    'tommy@capeandbay.com',
                    'blair@capeandbay.com',
                    'adam@capeandbay.com',
                    'david@capeandbay.com',
                    'clayton@capeandbay.com',
                    'chrys@capeandbay.com',
                ],
            ],
        ];

        foreach ($cnb_teams as $team) {
            $members = $team['members'];
            unset($team['members']);

            $new_team = CreateTeam::run($team);
            foreach (User::whereIn('email', $members)->get() as $user) {
                AddTeamMember::run($new_team->id, $user->id);
            }
        }

        if (env('RAPID_SEED') === true) {
            $clients      = Client::all()->keyBy('name');
            $client_teams = [
                // The Kalamazoo
                [
                    'name'      => 'Zoo Sales Team',
                    'home_team' => 0,
                    'client_id' => $clients['The Kalamazoo']->id,
                ],
            ];
        } else {
            $clients = Client::all()->keyBy('name');

            $client_teams = [
                // Fitness Truth
                [
                    'name'      => 'FitnessTruth Texas South',
                    'home_team' => 0,
                    'client_id' => $clients['FitnessTruth']->id,
                ],

                // The Z
                [
                    'name'      => 'Big Island Team',
                    'home_team' => 0,
                    'client_id' => $clients['The Z']->id,
                ],
                [
                    'name'      => 'Oahu Team',
                    'home_team' => 0,
                    'client_id' => $clients['The Z']->id,
                ],
                [
                    'name'      => 'Big Island Boating & CrossFit Club',
                    'home_team' => 0,
                    'client_id' => $clients['The Z']->id,
                ],

                // Stencils
                [
                    'name'      => 'Stencils Seattle',
                    'home_team' => 0,
                    'client_id' => $clients['Stencils']->id,
                ],
                [
                    'name'      => 'Stencils San Andreas',
                    'home_team' => 0,
                    'client_id' => $clients['Stencils']->id,
                ],
                [
                    'name'      => 'Stencils Portland',
                    'home_team' => 0,
                    'client_id' => $clients['Stencils']->id,
                ],
                [
                    'name'      => 'Stencils LA',
                    'home_team' => 0,
                    'client_id' => $clients['Stencils']->id,
                ],
                [
                    'name'      => 'Stencils San Diego',
                    'home_team' => 0,
                    'client_id' => $clients['Stencils']->id,
                ],
                [
                    'name'      => 'Stencils San Jose',
                    'home_team' => 0,
                    'client_id' => $clients['Stencils']->id,
                ],
                [
                    'name'      => 'Stencils Portland 2',
                    'home_team' => 0,
                    'client_id' => $clients['Stencils']->id,
                ],

                // SciFi Purple Gyms
                [
                    'name'      => 'SciFi NC',
                    'home_team' => 0,
                    'client_id' => $clients['Sci-Fi Purple Gyms']->id,
                ],
                [
                    'name'      => 'Purple FL',
                    'home_team' => 0,
                    'client_id' => $clients['Sci-Fi Purple Gyms']->id,
                ],

            ];
        }

        foreach ($client_teams as $team) {
            echo($team['name'] . PHP_EOL);
            CreateTeam::run($team);
        }
    }
}
