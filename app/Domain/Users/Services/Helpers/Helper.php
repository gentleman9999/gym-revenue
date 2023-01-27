<?php

declare(strict_types=1);

namespace App\Domain\Users\Services\Helpers;

use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\Customer;
use Illuminate\Database\Eloquent\Builder;

//TODO: this should just be handled via Team (or soon to be Location) Scopes.
class Helper
{
    public static function setUpCustomersObject(string $current_team_id, string $client_id = null): ?Builder
    {
        $customer_query = null;

        if ($client_id !== null) {
            $client = Client::find($client_id);

            if ($current_team_id != $client->home_team_id) {
                $team_locations = self::getTeamLocations($current_team_id);
                if (count($team_locations) > 0) {
                    $customer_query = Customer::whereIn('home_location_id', $team_locations);
                }
            } else {
                $customer_query = new Customer();
            }
        }

        return $customer_query;
    }

    public static function getLocations(string $current_team_id, bool $is_client_user, string $client_id = null): array
    {
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

        $locations_records = [];

        if ($client_id !== null) {
            $client = Client::find($client_id);
            // The active_team is the current client's default_team (gets all the client's locations)
            if ($current_team_id == $client->home_team_id) {
                $locations_records = Location::all();
            } else {
                $team_locations = self::getTeamLocations($current_team_id);
                if (count($team_locations) > 0) {
                    $locations_records = Location::whereIn('gymrevenue_id', $team_locations)->get();
                }
            }
        } else {
            // Cape & Bay user
            if (! $is_client_user) {
                $locations_records = Location::all();
            }
        }

        $locations = [];
        foreach ($locations_records as $location) {
            $locations[$location->gymrevenue_id] = $location->name;
        }

        return $locations;
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

    public static function getTeamLocations(string $current_team_id): array
    {
        return Team::find($current_team_id)->locations->toArray();
    }
}
