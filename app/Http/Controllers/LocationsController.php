<?php

namespace App\Http\Controllers;

use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
use App\Models\Clients\Location;
use App\Models\TeamDetail;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;


class LocationsController extends Controller
{
    protected $rules = [
        'name' => ['required', 'max:50'],
        'city' => ['required', 'max:30'],
        'state' => ['required', 'size:2'],
        'client_id' => ['required'],
        'address1' => 'required',
        'address2' => [],
        'zip' => ['required', 'size:5']
    ];

    //
    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();
        $userid =request()->user()->id;
        $teamid=request()->user()->current_team_id;
        $myrole=DB::select('select role from team_user where user_id='.$userid.' and team_id='.$teamid.'');
        foreach($myrole as $role){
        }
//dd(request()->user()->current_team_id,$client_id,$role);
        // @todo - insert Bouncer-based ACL here.

        $page_count = 10;

        if(!empty($locations = $this->setUpLocationsObject($is_client_user, $client_id)))
        {
            $locations = $locations->with('client')
                ->filter($request->only('search', 'trashed'))
                ->paginate($page_count);
        }

        if ((!is_null($client_id))) {
            $client = Client::find($client_id);
            $title = "{$client->name} Locations";
        } else {
            $title = 'All Client Locations';
        }


        return Inertia::render('Locations/Show', [
            'locations' => $locations,
            'title' => $title,
            'isClientUser' => $is_client_user,
            'role' =>$role,
            'filters' => $request->all('search', 'trashed','state')
        ]);
    }

    public function create()
    {
        return Inertia::render('Locations/Create', [
//            'locations' => Location::all(),
        ]);
    }

    public function edit($id)
    {
        if (!$id) {
            Alert::error("No Location ID provided")->flash();
            return Redirect::back();
        }

        return Inertia::render('Locations/Edit', [
            'location' => Location::find($id),
        ]);
    }


    public function store(Request $request)
    {
        $location = Location::create(
            $request->validate($this->rules)
        );
        Alert::success("Location '{$location->name}' was created")->flash();

        return Redirect::route('locations');
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No Location ID provided")->flash();
            return Redirect::route('locations');
        }

        $location = Location::findOrFail($id);
        $location->updateOrFail($request->validate($this->rules));
        Alert::success("Location '{$location->name}' updated")->flash();

        return Redirect::route('locations');
    }

    public function trash($id)
    {
        if (!$id) {
            Alert::error("No Location ID provided")->flash();
            return Redirect::route('locations');
        }

        $location = Location::findOrFail($id);

        $success = $location->deleteOrFail();

        Alert::success("Location '{$location->name}' trashed")->flash();

        //we need to update current_location_id where applicable
        $client = $location->client()->first();
        $default_location = $client->locations()->first();
        //TODO:we could add an optional default location ID in Clients/ClientDetails and/or Team/TeamDetails,
        // then fall back to the line above when not set
        //We could also show a modal trap, forcing them to select a new location before they can do anything in the app.
        //we will also need to do a similar operation with a user switches TEAMS
        if ($default_location !== null) {
            User::whereCurrentLocationId($id)->update(['current_location_id' => $default_location->id]);
        }

        return Redirect::back();
    }

    public function restore(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No Location ID provided")->flash();
            return Redirect::back();
        }
        $location = Location::withTrashed()->findOrFail($id);
        $location->restore();

        Alert::success("Location '{$location->name}' restored")->flash();

        return Redirect::back();
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
        if (!$request->user()->switchLocation($location)) {
            abort(403);
        }

        return redirect(config('fortify.home'), 303);
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

        /*$locations = (!is_null($client_id))
            ? Location::whereClientId($client_id)
            : new Location();
        */

        if((!is_null($client_id)))
        {
            $current_team = request()->user()->currentTeam()->first();
            $client = Client::whereId($client_id)->with('default_team_name')->first();
            $default_team_name = $client->default_team_name->value;

            // The active_team is the current client's default_team (gets all the client's locations)
            if($current_team->name == $default_team_name)
            {
                $results = Location::whereClientId($client_id);
            }
            else
            {
                // The active_team is not the current client's default_team
                $team_locations = TeamDetail::whereTeamId($current_team->id)
                    ->where('name', '=', 'team-location')->whereActive(1)
                    ->get();

                if(count($team_locations) > 0)
                {
                    $in_query = [];
                    // so get the teams listed in team_details
                    foreach($team_locations as $team_location)
                    {
                        $in_query[] = $team_location->value;
                    }

                    $results = Location::whereClientId($client_id)
                        ->whereIn('gymrevenue_id', $in_query);
                }
            }
        }
        else
        {
            // Cape & Bay user
            if(!$is_client_user)
            {
                $results = new Location();
            }
        }

        return $results;
    }
}
