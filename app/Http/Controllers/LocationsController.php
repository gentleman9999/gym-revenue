<?php

namespace App\Http\Controllers;

use App\Models\Clients\Client;
use App\Models\Clients\ClientDetail;
use App\Models\Clients\Location;
use App\Models\Clients\LocationDetails;
use App\Models\TeamDetail;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;
use Illuminate\Foundation\Http\FormRequest;

class LocationsController extends Controller
{
    protected $rules = [

        'poc_last' =>['sometimes'],
        'name' => ['required', 'max:50'],
        'city' => ['required', 'max:30'],
        'state' => ['required', 'size:2'],
        'client_id' => ['required'],
        'address1' => ['required','max:200'],
        'address2' => [],
        'zip' => ['required', 'size:5'],
        'phone' => [],
        'poc_first' => [],
        'poc_phone' => [],
        'opendate' => [],
        'closedate' => [],
        //'location_no' => ['required', 'max:50'],
        'gymrevenue_id' => [],
    ];

    //
    public function index(Request $request)
    {
        $client_id = request()->user()->currentClientId();
        $is_client_user = request()->user()->isClientUser();

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
            'filters' => $request->all('search', 'trashed')
        ]);
    }

    public function create()
    {
         return   Inertia::render('Locations/Create', [
//            'locations' => Location::all(),
        ]);
    }

    public function edit($id)
    {
        if (!$id) {
            Alert::error("No Location ID provided")->flash();
            return Redirect::back();
        }
        $locationdetails =[];
        $locationdetails = LocationDetails::where('location_id',$id)->get();
        $locationitems= [];
        $phone ='';
        $poc_first ='';
        $poc_last ='';
        $poc_phone ='';
        $opendate ='';
        $closedate ='';


         foreach ($locationdetails as $locationitems){
//dd($locationitems);
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


    public function store(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        $prefix = ClientDetail::whereClientId($client_id)->whereDetail('prefix')->pluck('value');
        $iterations = Location::whereClientId($client_id)->pluck('gymrevenue_id');
        $value = 001;

        if(Str::contains($iterations[count($iterations)-1], $prefix[0]))
                $value = (int) str_replace($prefix[0], "", $iterations[count($iterations)-1]) + 1;

        $request->merge(['gymrevenue_id' => $prefix[0].''.sprintf('%03d', $value)]);

        $location = Location::create(
            $request->validate($this->rules)
        );

//      dd($location->id,$request,$request->phone);

        if(!$location->id){
            Alert::error("No Location ID provided")->flash();
            return Redirect::route('locations');
        }
                if($request->phone) {
            LocationDetails::create(['location_id' => $location->id,
                    'client_id' => $client_id,
                    'field' => 'phone',
                    'value' => $request->phone,
                    'misc' =>  ['userid',request()->user()->id]
                ]
            );
        }

        if($request->poc_first) {
            LocationDetails::create(['location_id' => $location->id,
                    'client_id' => $client_id,
                    'field' => 'poc_first',
                    'value' => $request->poc_first,
                    'misc' => ['userid',request()->user()->id]
                ]
            );
        }
        if($request->poc_last) {
            LocationDetails::create(['location_id' => $location->id,
                    'client_id' => $client_id,
                    'field' => 'poc_last',
                    'value' => $request->poc_last,
                    'misc' => ['userid',request()->user()->id]
                ]
            );
        }
        if($request->poc_phone) {
            LocationDetails::create(['location_id' => $location->id,
                    'client_id' => $client_id,
                    'field' => 'poc_phone',
                    'value' => $request->poc_phone,
                    'misc' => ['userid',request()->user()->id]
                ]
            );
        }
        if($request->opendate) {
            LocationDetails::create(['location_id' => $location->id,
                    'client_id' => $client_id,
                    'field' => 'open_date',
                    'value' => $request->opendate,
                    'misc' => ['userid',request()->user()->id]
                ]
            );
        }
        if($request->closedate) {
            LocationDetails::create(['location_id' => $location->id,
                    'client_id' => $client_id,
                    'field' => 'close_date',
                    'value' => $request->closedate,
                    'misc' => ['userid',request()->user()->id]
                ]
            );
        }


        Alert::success("Location '{$location->name}' was created")->flash();

        return Redirect::route('locations');
    }

    public function update(Request $request, $id)
    {
      //  dd($request->validate($this->rules));

        if (!$id) {
            Alert::error("No Location ID provided")->flash();
            return Redirect::route('locations');
        }
        $client_id = request()->user()->currentClientId();
        $location = Location::findOrFail($id);
        $location->updateOrFail($request->validate($this->rules));
        //echo $this->validate(request()->all());


        $data =  request()->all();
     //   dd($data,$request,$request->phone,$client_id,$request->validate($this->rules));


        if($request->phone) {
            LocationDetails::create(['location_id' => $id,
                    'client_id' => $client_id,
                    'field' => 'phone',
                    'value' => $request->phone,
                    'misc' =>  ['userid',request()->user()->id]
                ]
            );
        }
        if($request->poc_first) {
            LocationDetails::create(['location_id' => $id,
                    'client_id' => $client_id,
                    'field' => 'poc_first',
                    'value' => $request->poc_first,
                    'misc' => ['userid',request()->user()->id]
                ]
            );
        }
        if($request->poc_last) {
            LocationDetails::create(['location_id' => $id,
                    'client_id' => $client_id,
                    'field' => 'poc_last',
                    'value' => $request->poc_last,
                    'misc' => ['userid',request()->user()->id]
                ]
            );
        }
        if($request->poc_phone) {
            LocationDetails::create(['location_id' => $id,
                    'client_id' => $client_id,
                    'field' => 'poc_phone',
                    'value' => $request->poc_phone,
                    'misc' => ['userid',request()->user()->id]
                ]
            );
        }
        if($request->opendate) {
            LocationDetails::create(['location_id' => $id,
                    'client_id' => $client_id,
                    'field' => 'open_date',
                    'value' => $request->opendate,
                    'misc' => ['userid',request()->user()->id]
                ]
            );
        }
        if($request->closedate) {
            LocationDetails::create(['location_id' => $id,
                    'client_id' => $client_id,
                    'field' => 'close_date',
                    'value' => $request->closedate,
                    'misc' => ['userid',request()->user()->id]
                ]
            );
        }






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
