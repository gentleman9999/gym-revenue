<?php

namespace Database\Seeders\Clients;

use App\Domain\Teams\Actions\AddTeamMember;
use App\Domain\Teams\Actions\CreateTeam;
use App\Domain\Users\Models\User;
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
                'client_id' => null,
                'name' => 'Cape & Bay Developers',
                'home_team' => 0,
                'members' => [
                    'angel@capeandbay.com',
                    'shivam@capeandbay.com',
                    'philip@capeandbay.com',
                    'sterling@capeandbay.com',
                    'tommy@capeandbay.com',
                    'blair@capeandbay.com',
                    'adam@capeandbay.com',
                    'david@capeandbay.com',
                    'larry@capeandbay.com',

                ],
            ],
        ];

        foreach ($cnb_teams as $team) {
            VarDumper::dump($team['name']);
            $members = $team['members'];
            unset($team['members']);
            $new_team = CreateTeam::run($team);

            if (count($members) > 0) {
                foreach ($members as $idx => $email) {
                    AddTeamMember::run($new_team, $email);

//                    $user = User::whereEmail($email)->first();
//                    $new_team->users()->attach($user);
//                    UserAggregate::retrieve($user->id)
//                        ->addToTeam($new_team->id, $new_team->name)
//                        ->persist();
                }
            }
        }

        $kalamazoo_owner = User::whereEmail('giraffe@kalamazoo.com')->first();
        $truth_owner = User::whereEmail('monyahanb@clubtruth.com')->first();
        $theZ_owner = User::whereEmail('malaki@thezclubs.com')->first();
        $stencils_owner = User::whereEmail('bsmith@stencils.net')->first();
        $scifi_owner = User::whereEmail('agabla@scifipurplegyms.com')->first();
        $ifit_owner = User::whereEmail('sherri@ifit.com')->first();

        $clients = \App\Domain\Clients\Models\Client::all()->keyBy('name');

        $client_teams = [
            // The Kalamazoo
            [
                'name' => 'Zoo Sales Team',
                'home_team' => 0,
                'client_id' => $clients['The Kalamazoo']->id,
            ],
            [
                'name' => 'The Kalamazoo Gym #1',
                'home_team' => 0,
                'client_id' => $clients['The Kalamazoo']->id,
            ],
            [
                'name' => 'The Kalamazoo Gym #2',
                'home_team' => 0,
                'client_id' => $clients['The Kalamazoo']->id,
            ],
            // Fitness Truth
            [
                'name' => 'FitnessTruth Texas South',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'FitnessTruth Texas Houston',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'FitnessTruth Texas West',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'FitnessTruth Tennessee',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'Location: Abbot',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'Location: Abernathy',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'Location: Abram',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'Location: Abbot 2',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'Location: Abernathy 2',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'Location: Abbot 3',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'Location: Abbot 4',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'Location: Abernathy 3',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'Location: Abbot 5',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],
            [
                'name' => 'Location: Adams',
                'home_team' => 0,
                'client_id' => $clients['FitnessTruth']->id,
            ],

            // The Z
            [
                'name' => 'Big Island Team',
                'home_team' => 0,
                'client_id' => $clients['The Z']->id,
            ],
            [
                'name' => 'Oahu Team',
                'home_team' => 0,
                'client_id' => $clients['The Z']->id,
            ],
            [
                'name' => 'Oahu Honolulu 1',
                'home_team' => 0,
                'client_id' => $clients['The Z']->id,
            ],
            [
                'name' => 'Oahu Honolulu 2',
                'home_team' => 0,
                'client_id' => $clients['The Z']->id,
            ],
            [
                'name' => 'Oahu Honolulu 3',
                'home_team' => 0,
                'client_id' => $clients['The Z']->id,
            ],
            [
                'name' => 'Oahu Honolulu 4',
                'home_team' => 0,
                'client_id' => $clients['The Z']->id,
            ],
            [
                'name' => 'Oahu Honolulu 5',
                'home_team' => 0,
                'client_id' => $clients['The Z']->id,
            ],
            [
                'name' => 'Big Island Boating & CrossFit Club',
                'home_team' => 0,
                'client_id' => $clients['The Z']->id,
            ],

            // TruFit

            // Stencils
            [
                'name' => 'Stencils Seattle',
                'home_team' => 0,
                'client_id' => $clients['Stencils']->id,
            ],
            [
                'name' => 'Stencils San Andreas',
                'home_team' => 0,
                'client_id' => $clients['Stencils']->id,
            ],
            [
                'name' => 'Stencils Portland',
                'home_team' => 0,
                'client_id' => $clients['Stencils']->id,
            ],
            [
                'name' => 'Stencils LA',
                'home_team' => 0,
                'client_id' => $clients['Stencils']->id,
            ],
            [
                'name' => 'Stencils San Diego',
                'home_team' => 0,
                'client_id' => $clients['Stencils']->id,
            ],
            [
                'name' => 'Stencils San Jose',
                'home_team' => 0,
                'client_id' => $clients['Stencils']->id,
            ],
            [
                'name' => 'Stencils San Jose',
                'home_team' => 0,
                'client_id' => $clients['Stencils']->id,
            ],
            [
                'name' => 'Stencils Portland',
                'home_team' => 0,
                'client_id' => $clients['Stencils']->id,
            ],
            [
                'name' => 'Stencils Portland 2',
                'home_team' => 0,
                'client_id' => $clients['Stencils']->id,
            ],
            [
                'name' => 'Stencils @ Microsoft',
                'home_team' => 0,
                'client_id' => $clients['Stencils']->id,
            ],

            // SciFi Purple Gyms
            [
                'name' => 'SciFi NC',
                'home_team' => 0,
                'client_id' => $clients['Sci-Fi Purple Gyms']->id,
            ],
            [
                'name' => 'Purple FL',
                'home_team' => 0,
                'client_id' => $clients['Sci-Fi Purple Gyms']->id,
            ],
            [
                'name' => 'Gym Advance A',
                'home_team' => 0,
                'client_id' => $clients['Sci-Fi Purple Gyms']->id,
            ],
            [
                'name' => 'Gym BeachLyfe',
                'home_team' => 0,
                'client_id' => $clients['Sci-Fi Purple Gyms']->id,
            ],
            [
                'name' => 'Gym Advance B',
                'home_team' => 0,
                'client_id' => $clients['Sci-Fi Purple Gyms']->id,
            ],
            [
                'name' => 'Gym Callahan',
                'home_team' => 0,
                'client_id' => $clients['Sci-Fi Purple Gyms']->id,
            ],

            //iFit
            [
                'name' => 'iFit Florida',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'iFit Georgia',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'iFit Virginia',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'iFit Sales Team',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'iFit Sales Team Georgia/VA',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'iFit Sales Team Florida',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'FL - Tampa 1',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'FL - Lake City',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'FL - Hilliard',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'FL - Orange Park',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'FL - Tampa 2',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'GA - Atlanta 1',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'GA - Atlanta 2',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'GA - Atlanta 16',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'VA - Va Beach 1',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
            [
                'name' => 'VA - Va Beach 2',
                'home_team' => 0,
                'client_id' => $clients['iFit']->id,
            ],
        ];

        foreach ($client_teams as $team) {
            VarDumper::dump($team['name']);
            CreateTeam::run($team);
        }
    }
}
