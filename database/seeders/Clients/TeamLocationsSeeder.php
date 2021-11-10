<?php

namespace Database\Seeders\Clients;

use App\Models\Team;
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
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Florida'),
                'name' => 'team-location',
                'value' => 'IF30',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Florida'),
                'name' => 'team-location',
                'value' => 'IF31',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Florida'),
                'name' => 'team-location',
                'value' => 'IF32',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Florida'),
                'name' => 'team-location',
                'value' => 'IF33',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Florida'),
                'name' => 'team-location',
                'value' => 'IF34',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Georgia'),
                'name' => 'team-location',
                'value' => 'IF35',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Georgia'),
                'name' => 'team-location',
                'value' => 'IF36',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Georgia'),
                'name' => 'team-location',
                'value' => 'IF37',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Virginia'),
                'name' => 'team-location',
                'value' => 'IF38',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Virginia'),
                'name' => 'team-location',
                'value' => 'IF39',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Sales Team'),
                'name' => 'team-location',
                'value' => 'IF30',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Sales Team'),
                'name' => 'team-location',
                'value' => 'IF31',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Sales Team'),
                'name' => 'team-location',
                'value' => 'IF32',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Sales Team'),
                'name' => 'team-location',
                'value' => 'IF33',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Sales Team'),
                'name' => 'team-location',
                'value' => 'IF34',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Sales Team'),
                'name' => 'team-location',
                'value' => 'IF35',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Sales Team'),
                'name' => 'team-location',
                'value' => 'IF36',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Sales Team'),
                'name' => 'team-location',
                'value' => 'IF37',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Sales Team'),
                'name' => 'team-location',
                'value' => 'IF38',
            ],
            [
                'team_id' => Team::fetchTeamIDFromName( 'iFit Sales Team'),
                'name' => 'team-location',
                'value' => 'IF39',
            ],

            [
                'team_id' => Team::fetchTeamIDFromName( 'Zoo Sales Team'),
                'name' => 'team-location',
                'value' => 'TK12',
            ],
        ];

        foreach ($client_teams as $client_team)
        {
            VarDumper::dump($client_team['value']);
            TeamDetail::firstOrCreate($client_team);
        }
    }
}
