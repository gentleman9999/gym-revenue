<?php

namespace App\Http\Controllers;

use App\Actions\Clients\Locations\GenerateGymRevenueId;
use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
use App\Models\Clients\Location;
use App\Models\Clients\LocationDetails;
use App\Models\TeamDetail;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Foundation\Http\FormRequest;
use Silber\Bouncer\Bouncer;


class LocationsController extends Controller
{

    public function index(Request $request)
    {
        $user = request()->user();
        $client_id = $user->currentClientId();
        $is_client_user = $user->isClientUser();
        $page_count = 10;

        if(is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        if($user->cannot('locations.read', Location::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

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
            'filters' => $request->all('search', 'trashed'),
            'clientId' => $client_id
        ]);
    }

    public function create()
    {
        $user = request()->user();
        if($user->cannot('locations.create', Location::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        return Inertia::render('Locations/Create', [
        ]);
    }

    public function edit($id)
    {
        $user = request()->user();
        if($user->cannot('locations.update', Location::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        if (!$id) {
            Alert::error("No Location ID provided")->flash();
            return Redirect::back();
        }

        $locationDetails = LocationDetails::where('location_id',$id)->get();
        $phone = $poc_first = $poc_last = $poc_phone = $opendate = $closedate = '';

         foreach ($locationDetails as $locationitems)
         {
             if($locationitems->field == 'phone') {
                 $phone = $locationitems->value;
             }
             if($locationitems->field == 'poc_first') {
                 $poc_first = $locationitems->value;
             }
             if($locationitems->field == 'poc_last') {
                 $poc_last = $locationitems->value;
             }
             if($locationitems->field == 'poc_phone') {
                 $poc_phone = $locationitems->value;
             }
             if($locationitems->field == 'open_date') {
                 $opendate = $locationitems->value;
             }
             if($locationitems->field == 'close_date') {
                 $closedate = $locationitems->value;
             }
        }

        return Inertia::render('Locations/Edit', [
            'location' => Location::find($id),
            'phone' => $phone,
            'poc_first' => $poc_first,
            'poc_last' => $poc_last,
            'poc_phone' => $poc_phone,
            'opendate' => $opendate,
            'closedate' => $closedate,
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
            if($current_team->id == $default_team_name)
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

    public function view($id)
    {
        $user = request()->user();
        if($user->cannot('locations.read', Location::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        if (!$id) {
            Alert::error("No Location ID provided")->flash();
            return Redirect::back();
        }

        $locationDetails = LocationDetails::where('location_id',$id)->get();
        $phone = $poc_first = $poc_last = $poc_phone = $opendate = $closedate = '';

        foreach ($locationDetails as $locationitems)
        {
            if($locationitems->field == 'phone') {
                $phone = $locationitems->value;
            }
            if($locationitems->field == 'poc_first') {
                $poc_first = $locationitems->value;
            }
            if($locationitems->field == 'poc_last') {
                $poc_last = $locationitems->value;
            }
            if($locationitems->field == 'poc_phone') {
                $poc_phone = $locationitems->value;
            }
            if($locationitems->field == 'open_date') {
                $opendate = $locationitems->value;
            }
            if($locationitems->field == 'close_date') {
                $closedate = $locationitems->value;
            }
        }

        $data = Location::findOrFail($id)->toArray();
        $data['phone'] = $phone;
        $data['poc_first'] = $poc_first;
        $data['poc_last'] = $poc_last;
        $data['poc_phone'] = $poc_phone;
        $data['opendate'] = $opendate;
        $data['closedate'] = $closedate;

        return $data;
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $user = request()->user();
        $client_id = $user->currentClientId();
        $is_client_user = $user->isClientUser();

        if(is_null($client_id)) {
            return Redirect::route('dashboard');
        }

        if($user->cannot('locations.read', Location::class))
        {
            abort(403);
        }

        if(!empty($locations = $this->setUpLocationsObject($is_client_user, $client_id)))
        {
            $locations = $locations->with('client')
                ->filter($request->only('search', 'trashed'))
                ->get();
        }

        return $locations;

    }


}
