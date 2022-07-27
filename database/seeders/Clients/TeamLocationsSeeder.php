<?php

namespace Database\Seeders\Clients;

use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Actions\UpdateTeam;
use App\Domain\Teams\Models\Team;
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
            // The Z
            [
                'team_id' => Team::fetchTeamIDFromName('Big Island Team'),
                'name' => 'team-location',
                'value' => 'TZ04',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Oahu Team'),
                'name' => 'team-location',
                'value' => 'TZ01',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Oahu Honolulu 1'),
                'name' => 'team-location',
                'value' => 'TZ01',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Oahu Honolulu 2'),
                'name' => 'team-location',
                'value' => 'TZ01',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Oahu Honolulu 3'),
                'name' => 'team-location',
                'value' => 'TZ01',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Oahu Honolulu 4'),
                'name' => 'team-location',
                'value' => 'TZ01',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Oahu Honolulu 5'),
                'name' => 'team-location',
                'value' => 'TZ01',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Big Island Boating & CrossFit Club'),
                'name' => 'team-location',
                'value' => 'TZ01',
            ],
            // TruFit
            // Stencils
            [
                'team_id' => Team::fetchTeamIDFromName('Stencils Portland'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('ST01')->first()->gymrevenue_id,
            ],
            // SciFi Purple Gyms
            [
                'team_id' => Team::fetchTeamIDFromName('SciFi NC'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('PF01')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Purple FL'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('PF01')->first()->gymrevenue_id,
            ],

            [
                'team_id' => Team::fetchTeamIDFromName('Gym Advance A'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('PF02')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Gym BeachLyfe'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('PF02')->first()->gymrevenue_id,
            ],

            [
                'team_id' => Team::fetchTeamIDFromName('Gym Advance B'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('PF03')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('Gym Callahan'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('PF03')->first()->gymrevenue_id,
            ],
            // iFit
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Florida'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I01')->first()->gymrevenue_id,
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
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I02')->first()->gymrevenue_id,
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
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I04')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Florida'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I05')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('FL - Tampa 2'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I05')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Georgia'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I06')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('GA - Atlanta 1'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I06')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Georgia'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I13')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('GA - Atlanta 2'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I13')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Georgia'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I12')->first()->gymrevenue_id,
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
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I11')->first()->gymrevenue_id,
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
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I01')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Florida'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I01')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I02')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Florida'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I02')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I03')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Florida'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I03')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I04')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Florida'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I04')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I05')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Florida'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I05')->first()->gymrevenue_id,
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
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I13')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I12')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Georgia/VA'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I12')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => Location::whereLocationNo('I11')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Georgia/VA'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I11')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I10')->first()->gymrevenue_id,
            ],
            [
                'team_id' => Team::fetchTeamIDFromName('iFit Sales Team Georgia/VA'),
                'name' => 'team-location',
                'value' => \App\Domain\Locations\Projections\Location::whereLocationNo('I10')->first()->gymrevenue_id,
            ],
        ];

        foreach ($client_teams as $client_team) {
            VarDumper::dump($client_team['value']);
            UpdateTeam::run(Team::findOrFail($client_team['team_id']), ['locations' => [$client_team['value']]]);
        }
    }
}
