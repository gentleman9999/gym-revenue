<?php

namespace Database\Seeders\Clients;

use App\Domain\Teams\Models\Team;
use App\Models\Clients\Location;
use App\Models\TeamDetail;
use Illuminate\Database\Seeder;
use Symfony\Component\VarDumper\VarDumper;

class TeamLocationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client_teams = [
            // The Kalamazoo
            [
                'team_id' => Team::fetchTeamIDFromName('Zoo Sales Team'),
                'name' => 'team-location',
                'value' => 'TKH001',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('The Kalamazoo Gym #1'),
                'name' => 'team-location',
                'value' => 'TKH001',
            ],
            // Bodies By Brett
            [
                'team_id' => Team::fetchTeamIDFromName('Sales Bodies'),
                'name' => 'team-location',
                'value' => 'BBB001',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Sales Bodies'),
                'name' => 'team-location',
                'value' => 'BBB002',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Sales Bodies'),
                'name' => 'team-location',
                'value' => 'BBB003',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Tampa 1'),
                'name' => 'team-location',
                'value' => 'BBB001',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Tampa 2'),
                'name' => 'team-location',
                'value' => 'BBB003',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Tampa 1'),
                'name' => 'team-location',
                'value' => 'BBB003',
            ],

            // The Z
            // TruFit
            // Stencils
            [
                'team_id' => Team::fetchTeamIDFromName('Stencils Portland'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('ST01')->first()->gymrevenue_id,
            ],
            // SciFi Purple Gyms
            // iFit
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Florida'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I01')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('FL - Tampa 1'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I01')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Florida'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I02')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('FL - Lake City'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I02')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Florida'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I03')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('FL - Hilliard'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I03')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Florida'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I04')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('FL - Orange Park'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I04')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Florida'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I05')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('FL - Tampa 2'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I05')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Georgia'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I06')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('GA - Atlanta 1'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I06')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Georgia'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I13')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('GA - Atlanta 2'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I13')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Georgia'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I12')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('GA - Atlanta 16'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I12')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Virginia'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I11')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('VA - Va Beach 1'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I11')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Virginia'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I10')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('VA - Va Beach 2'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I10')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I01')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Florida'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I01')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I02')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Florida'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I02')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I03')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Florida'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I03')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I04')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Florida'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I04')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I05')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Florida'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I05')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I06')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Georgia/VA'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I06')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I13')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Georgia/VA'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I13')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I12')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Georgia/VA'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I12')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I11')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Georgia/VA'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I11')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I10')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Georgia/VA'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I10')->first()->gymrevenue_id,
            ],
        ];

        foreach ($client_teams as $client_team) {
            VarDumper::dump($client_team['value']);
            TeamDetail::firstOrCreate($client_team);
        }
    }
}
