<?php

declare(strict_types=1);

namespace App\Domain\Users\Services\Helpers;

use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\Customer;
use Illuminate\Database\Eloquent\Builder;

class Helper
{
    public static function setUpCustomersObject(string $current_team_id, string $client_id = null): Builder
    {
        $results = [];

        if ($client_id !== null) {
            $client = Client::find($client_id);

            $team_locations = [];

            if ($current_team_id != $client->home_team_id) {
                $team_locations = Team::find($current_team_id)->locations();

                if (count($team_locations) > 0) {
                    $results = Customer::whereIn('home_location_id', $team_locations);
                }
            } else {
                $results = new Customer();
            }
        }

        return $results;
    }

    public static function setUpLocationsObject(string $current_team_id, bool $is_client_user, string $client_id = null): Builder | Location
    {
        $results = [];
        /**
         * BUSINESS RULES
         * 1. All Locations
         *  - Cape & Bay user
         *  - The active_team is the current client's default_team (gets all the client's locations)
         * 2. Scoped Locations
         *  - The active_team is not the current client's default_team
         *      so get the teams listed in team_details
         * 3. No Locations
         *  - The active_team is not the current client's default_team
         *      but there are no locations assigned in team_details
         *  - (Bug or Feature?) - The current client is null (cape & bay)
         *      but the user is not a cape & bay user.
         */


        if ($client_id !== null) {
            $client = Client::find($client_id);

            // The active_team is the current client's default_team (gets all the client's locations)
            if ($current_team_id == $client->home_team_id) {
                $results = new Location();
            } else {
                // The active_team is not the current client's default_team
                $team_locations = Team::find($current_team_id)->locations();

                if (count($team_locations) > 0) {
                    $results = Location::whereIn('gymrevenue_id', $team_locations);
                }
            }
        } else {
            // Cape & Bay user
            if (! $is_client_user) {
                $results = new Location();
            }
        }

        return $results;
    }

    public static function getCurrentTeam(string $default_team_id): Team
    {
        //@TODO: we may want to embed the currentClientId in the form as a field
        //instead of getting the value here.  if you have multiple tabs open, and
        // one has an outdated currentClient id, creating would have unintended ]
        //consequences, potentially adding the customer to the wrong client, or
        //just error out. also check for other areas in the app for similar behavior

        $team_id = $default_team_id;

        $session_team = session()->get('current_team');
        if ($session_team && array_key_exists('id', $session_team)) {
            $team_id = $session_team['id'];
        }

        return Team::find($team_id);
    }
}
