<?php

namespace App\Http\Controllers;

use App\Models\Clients\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class LocationsController extends Controller
{
    protected $rules = [
        'name' => ['required', 'max:50'],
        'city' => ['required', 'max:30'],
        'state' => ['required', 'size:2'],
        'client_id' => ['required'],
        'address1' => 'required',
        'address2' => [],
        'zip' => 'required'
    ];

    //
    public function index()
    {
        $client_id = request()->user()->currentClientId();

        // @todo - insert Bouncer-based ACL here.
        $locations = (!is_null($client_id))
            ? Location::whereClientId($client_id)->get()
            : Location::all();

        return Inertia::render('Locations/Show', [
            'locations' => $locations,
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
            //TODO:flash error
            return Redirect::route('locations');
        }

        return Inertia::render('Locations/Edit', [
            'location' => Location::find($id),
        ]);
    }


    public function store(Request $request)
    {
        Location::create(
            $request->validate($this->rules)
        );

        return Redirect::route('locations');
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            //TODO:flash error
            return Redirect::route('locations');
        }
        $location = $request->validate($this->rules);

        Location::findOrFail($id)->updateOrFail($location);

        return Redirect::route('locations');
    }

    public function delete(Request $request, $id)
    {
        if (!$id) {
            //TODO:flash error
            return Redirect::route('locations');
        }

        $location = Location::findOrFail($id);
        $location->deleteOrFail();
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

        return Redirect::route('locations');
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
}
