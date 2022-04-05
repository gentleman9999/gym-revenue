<?php

namespace App\Http\Controllers;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Classification;
use Bouncer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;
use Silber\Bouncer\Database\Role;

class ClassificationsController extends Controller
{
    protected $rules = [
        'name' => ['string', 'required'],
        'id' => ['integer', 'sometimes', 'nullable'],
        'ability_ids' => ['array', 'sometimes'],
        'ability_ids.*' => ['array', 'sometimes'],
    ];

    public function index(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        if (!$client_id) {
            return Redirect::route('dashboard');
        }
        if(request()->user()->cannot('classifications.read', Classification::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        $classifications = Classification::whereClientId($client_id)
            ->filter($request->only('search', 'trashed'))
            ->paginate(10);

        return Inertia::render('Classifications/Show', [
            'classifications' => $classifications,
            'filters' => $request->all('search', 'trashed', 'state')
        ]);
    }

    public function create()
    {
        $client_id = request()->user()->currentClientId();
        if (!$client_id) {
            return Redirect::route('dashboard');
        }
        if(request()->user()->cannot('classifications.create', Classification::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }
        return Inertia::render('Classifications/Create', [
        ]);
    }

    public function edit($id)
    {
        $client_id = request()->user()->currentClientId();
        if (!$client_id) {
            return Redirect::route('dashboard');
        }
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::back();
        }
        if(request()->user()->cannot('classifications.update', Classification::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }
        return Inertia::render('Classifications/Edit', [
            'classification' => Classification::findOrFail($id),
        ]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        if (!$client_id) {
            abort(403);
        }
        if(request()->user()->cannot('classifications.read', Classification::class))
        {
            abort(403);
        }

        $classifications = Classification::whereClientId($client_id)->get();

        return $classifications;

    }



}
