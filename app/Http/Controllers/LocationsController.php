<?php

namespace App\Http\Controllers;

use App\Models\Clients\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class LocationsController extends Controller
{
    //
    public function index()
    {
        return Inertia::render('Locations/Show', [
            'locations' => Location::all(),
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
        if(!$id){
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
            $request->validate([
                'name' => ['required', 'max:50'],
                'client_id' => ['required'],
            ])
        );

        return Redirect::route('locations');
    }

    public function update(Request $request, $id)
    {
        if(!$id){
            //TODO:flash error
            return Redirect::route('locations');
        }
        $location = $request->validate([
            'name' => ['required', 'max:50'],
            'client_id' => ['required'],
            'id' => ['required']
        ]);
        Location::findOrFail($id)->updateOrFail($location);

        return Redirect::route('locations');
    }
    public function delete(Request $request, $id)
    {
        if(!$id){
            //TODO:flash error
            return Redirect::route('locations');
        }

        Location::findOrFail($id)->deleteOrFail();

        return Redirect::route('locations');
    }

    /**
     * Update the authenticated user's current location.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function switch(Request $request)
    {
        $location = Location::findOrFail($request->location_id);
        if (! $request->user()->switchLocation($location)) {
            abort(403);
        }

        return redirect(config('fortify.home'), 303);
    }
}
