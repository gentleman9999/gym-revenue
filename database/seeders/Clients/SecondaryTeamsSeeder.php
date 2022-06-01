<?php

namespace Database\Seeders\Clients;

use App\Actions\Jetstream\CreateTeam;
use App\Aggregates\Users\UserAggregate;
use App\Models\Clients\Client;
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
                'default_team' => 0,
                'members' => [
                    'angel@capeandbay.com',
                    'shivam@capeandbay.com',
                    'philip@capeandbay.com',
                    'sterling@capeandbay.com',
                    'tommy@capeandbay.com',
                    'blair@capeandbay.com',
                    'david@capeandbay.com',
                ],
            ],
        ];

        foreach ($cnb_teams as $team) {
            VarDumper::dump($team['name']);
            $members = $team['members'];
            unset($team['members']);
            $new_team = Team::firstOrCreate($team);

            if (count($members) > 0) {
                foreach ($members as $idx => $email) {
                    $user = User::whereEmail($email)->first();
                    $new_team->users()->attach($user);
                    UserAggregate::retrieve($user->id)
                        ->addUserToTeam($new_team->id, $new_team->name, null)
                        ->persist();
                }
            }
        }

        $kalamazoo_owner = User::whereEmail('giraffe@kalamazoo.com')->first();
        $bodies_owner = User::whereEmail('brett+bbb@capeandbay.com')->first();
        $truth_owner = User::whereEmail('monyahanb@clubtruth.com')->first();
        $theZ_owner = User::whereEmail('malaki@thezclubs.com')->first();
        $stencils_owner = User::whereEmail('bsmith@stencils.net')->first();
        $scifi_owner = User::whereEmail('agabla@scifipurplegyms.com')->first();
        $ifit_owner = User::whereEmail('sherri@ifit.com')->first();

        $client_teams = [
            // The Kalamazoo
            [
                'user_id' => $kalamazoo_owner->id ?? 1,
                'name' => 'Zoo Sales Team',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'The Kalamazoo')->first()->id,
            ],
            [
                'user_id' => $kalamazoo_owner->id ?? 1,
                'name' => 'The Kalamazoo Gym #1',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'The Kalamazoo')->first()->id,
            ],
            [
                'user_id' => $kalamazoo_owner->id ?? 1,
                'name' => 'The Kalamazoo Gym #2',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'The Kalamazoo')->first()->id,
            ],

            // Bodies By Brett
            [
                'user_id' => $bodies_owner->id ?? 1,
                'name' => 'Sales Bodies',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Bodies By Brett')->first()->id,
            ],
            [
                'user_id' => $bodies_owner->id ?? 1,
                'name' => 'Tampa 1',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Bodies By Brett')->first()->id,
            ],
            [
                'user_id' => $bodies_owner->id ?? 1,
                'name' => 'Tampa 2',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Bodies By Brett')->first()->id,
            ],
            [
                'user_id' => $bodies_owner->id ?? 1,
                'name' => 'Tampa 3',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Bodies By Brett')->first()->id,
            ],

            // Fitness Truth
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'FitnessTruth Texas South',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'FitnessTruth Texas Houston',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'FitnessTruth Texas West',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'FitnessTruth Tennessee',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'Location: Abbot',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'Location: Abernathy',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'Location: Abram',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'Location: Abbot 2',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'Location: Abernathy 2',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'Location: Abbot 3',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'Location: Abbot 4',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'Location: Abernathy 3',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'Location: Abbot 5',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],
            [
                'user_id' => $truth_owner->id ?? 1,
                'name' => 'Location: Adams',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'FitnessTruth')->first()->id,
            ],

            // The Z
            [
                'user_id' => $theZ_owner->id ?? 1,
                'name' => 'Big Island Team',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'The Z')->first()->id,
            ],
            [
                'user_id' => $theZ_owner->id ?? 1,
                'name' => 'Oahu Team',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'The Z')->first()->id,
            ],
            [
                'user_id' => $theZ_owner->id ?? 1,
                'name' => 'Oahu Honolulu 1',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'The Z')->first()->id,
            ],
            [
                'user_id' => $theZ_owner->id ?? 1,
                'name' => 'Oahu Honolulu 2',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'The Z')->first()->id,
            ],
            [
                'user_id' => 14,
                'name' => 'Oahu Honolulu 3',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'The Z')->first()->id,
            ],
            [
                'user_id' => $theZ_owner->id ?? 1,
                'name' => 'Oahu Honolulu 4',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'The Z')->first()->id,
            ],
            [
                'user_id' => $theZ_owner->id ?? 1,
                'name' => 'Oahu Honolulu 5',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'The Z')->first()->id,
            ],
            [
                'user_id' => $theZ_owner->id ?? 1,
                'name' => 'Big Island Boating & CrossFit Club',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'The Z')->first()->id,
            ],

            // TruFit

            // Stencils
            [
                'user_id' => $stencils_owner->id ?? 1,
                'name' => 'Stencils Seattle',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Stencils')->first()->id,
            ],
            [
                'user_id' => $stencils_owner->id ?? 1,
                'name' => 'Stencils San Andreas',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Stencils')->first()->id,
            ],
            [
                'user_id' => $stencils_owner->id ?? 1,
                'name' => 'Stencils Portland',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Stencils')->first()->id,
            ],
            [
                'user_id' => $stencils_owner->id ?? 1,
                'name' => 'Stencils LA',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Stencils')->first()->id,
            ],
            [
                'user_id' => $stencils_owner->id ?? 1,
                'name' => 'Stencils San Diego',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Stencils')->first()->id,
            ],
            [
                'user_id' => $stencils_owner->id ?? 1,
                'name' => 'Stencils San Jose',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Stencils')->first()->id,
            ],
            [
                'user_id' => $stencils_owner->id ?? 1,
                'name' => 'Stencils San Jose',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Stencils')->first()->id,
            ],
            [
                'user_id' => $stencils_owner->id ?? 1,
                'name' => 'Stencils Portland',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Stencils')->first()->id,
            ],
            [
                'user_id' => $stencils_owner->id ?? 1,
                'name' => 'Stencils Portland 2',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Stencils')->first()->id,
            ],
            [
                'user_id' => $stencils_owner->id ?? 1,
                'name' => 'Stencils @ Microsoft',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Stencils')->first()->id,
            ],

            // SciFi Purple Gyms
            [
                'user_id' => $scifi_owner->id ?? 1,
                'name' => 'SciFi NC',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Sci-Fi Purple Gyms')->first()->id,
            ],
            [
                'user_id' => $scifi_owner->id ?? 1,
                'name' => 'Purple FL',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Sci-Fi Purple Gyms')->first()->id,
            ],
            [
                'user_id' => $scifi_owner->id ?? 1,
                'name' => 'Gym Advance A',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Sci-Fi Purple Gyms')->first()->id,
            ],
            [
                'user_id' => $scifi_owner->id ?? 1,
                'name' => 'Gym BeachLyfe',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Sci-Fi Purple Gyms')->first()->id,
            ],
            [
                'user_id' => $scifi_owner->id ?? 1,
                'name' => 'Gym Advance B',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Sci-Fi Purple Gyms')->first()->id,
            ],
            [
                'user_id' => $scifi_owner->id ?? 1,
                'name' => 'Gym Callahan',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'Sci-Fi Purple Gyms')->first()->id,
            ],

            //iFit
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'iFit Florida',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'iFit Georgia',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'iFit Virginia',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'iFit Sales Team',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'iFit Sales Team Georgia/VA',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'iFit Sales Team Florida',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'FL - Tampa 1',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'FL - Lake City',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'FL - Hilliard',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'FL - Orange Park',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'FL - Tampa 2',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'GA - Atlanta 1',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'GA - Atlanta 2',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'GA - Atlanta 16',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'VA - Va Beach 1',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
            [
                'user_id' => $ifit_owner->id ?? 1,
                'name' => 'VA - Va Beach 2',
                'personal_team' => 0,
                'default_team' => 0,
                'client_id' => Client::where('name', 'iFit')->first()->id,
            ],
        ];

        foreach ($client_teams as $team) {
            VarDumper::dump($team['name']);
            CreateTeam::run($team);

            UserAggregate::retrieve($team['user_id'])
                ->addUserToTeam($new_team->id, $new_team->name, $team['client_id'])
                ->persist();
        }
    }
}
