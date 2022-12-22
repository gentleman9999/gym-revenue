<?php

namespace App\Support;

use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Models\Team;

class CurrentInfoRetriever
{
    public static function getCurrentTeam(): Team
    {
        return Team::find(self::getCurrentTeamID());
    }

    public static function getCurrentTeamID(): string
    {
        $session_team = session()->get('current_team');

        return $session_team && array_key_exists('id', $session_team) ?
            $session_team['id'] : $team_id = auth()->user()->default_team_id;
    }

    public static function getCurrentClient(): Client
    {
        $session_client_id = self::getCurrentClientID();

        return $session_client_id ?
            Client::find(self::getCurrentClientID()) : null;
    }

    public static function getCurrentClientID(): ?string
    {
        return session()->get('client_id');
    }

    public static function getCurrentLocation(): ?Location
    {
        $session_location_id = sefl::getCurrentLocationID();

        return $session_location_id ?
            Location::findOrFail($session_location_id) : null;
    }

    public static function getCurrentLocationID(): ?string
    {
        return session()->get('current_location_id');
    }
}
