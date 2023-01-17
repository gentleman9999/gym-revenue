<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Support\CurrentInfoRetriever;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class LocationsController extends Controller
{
    public function index(Request $request)
    {
        $user = request()->user();
        $client_id = $user->client_id;
        $is_client_user = $user->isClientUser();
        $page_count = 10;

        if (is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        if ($user->cannot('locations.read', Location::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        if (! empty($locations = $this->setUpLocationsObject($is_client_user, $client_id))) {
            $locations = $locations
                ->filter($request->only('search', 'closed'))
                ->sort()
                ->paginate($page_count)
                ->appends(request()->except('page'));
        }

        if ((! is_null($client_id))) {
            $client = Client::find($client_id);
            $title = "{$client->name} Locations";
        } else {
            $title = 'All Client Locations';
        }

        return Inertia::render('Locations/Show', [
            'title' => $title,
            'isClientUser' => $is_client_user,
            'filters' => $request->all('search', 'closed'),
            'clientId' => $client_id,
        ]);
    }

    public function create()
    {
        $user = request()->user();
        if ($user->cannot('locations.create', Location::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Locations/Create');
    }

    public function edit(Location $location)
    {
        $user = request()->user();
        if ($user->cannot('locations.update', Location::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Locations/Edit', [
            'id' => $location->id,
        ]);
    }

    /**
     * Update the authenticated user's current location.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request)
    {
        $location = Location::findOrFail($request->location_id);

        /**
         * Need to add logic to check if user belongs to the team of this location
         */

        session()->put('current_location_id', $location->id);

        return redirect(config('fortify.home'), 303);
    }

    public function switchCalendar(Request $request)
    {
        $location = Location::findOrFail($request->location_id);
        if (! $request->user()->switchLocation($location)) {
            abort(403);
        }

        return Redirect::route('calendar');
    }

    private function setUpLocationsObject(bool $is_client_user, string $client_id = null)
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


        if ((! is_null($client_id))) {
            $current_team = CurrentInfoRetriever::getCurrentTeam();
            $client = Client::find($client_id);


            // The active_team is the current client's default_team (gets all the client's locations)
            if ($current_team->id == $client->home_team_id) {
                $results = new Location();
            } else {
                // The active_team is not the current client's default_team
                $team_locations = $current_team->locations();

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

    public function view(Location $location)
    {
        $user = request()->user();
        if ($user->cannot('locations.read', Location::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $location_details = $location->details;
        $poc_first = $poc_last = $poc_phone = '';

        foreach ($location_details as $location_detail) {
            if ($location_detail['field'] == 'poc_first') {
                $poc_first = $location_detail['value'];
            }
            if ($location_detail['field'] == 'poc_last') {
                $poc_last = $location_detail['value'];
            }
            if ($location_detail['field'] == 'poc_phone') {
                $poc_phone = $location_detail['value'];
            }
        }

        $data = $location->toArray();
        $data['poc_first'] = $poc_first;
        $data['poc_last'] = $poc_last;
        $data['poc_phone'] = $poc_phone;

        return $data;
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $user = request()->user();
        $client_id = $user->client_id;
        $is_client_user = $user->isClientUser();

        if (is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        if ($user->cannot('locations.read', Location::class)) {
            abort(403);
        }

        if (! empty($locations = $this->setUpLocationsObject($is_client_user, $client_id))) {
            $locations = $locations
                ->filter($request->only('search', 'closed'))
                ->get();
        }

        return $locations;
    }
}
