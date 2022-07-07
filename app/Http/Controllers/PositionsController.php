<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class PositionsController extends Controller
{
    public function index(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        if (! $client_id) {
            return Redirect::route('dashboard');
        }
        if (request()->user()->cannot('positions.read', Position::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $pos = Position::whereClientId($client_id)
            ->filter($request->only('search', 'trashed'))
            ->sort()
            ->paginate(10)
            ->appends(request()->except('page'));

        return Inertia::render('Positions/Show', [
            'position' => $pos,
            'filters' => $request->all('search', 'trashed', 'state'),
        ]);
    }

    public function create()
    {
        $client_id = request()->user()->currentClientId();
        if (! $client_id) {
            return Redirect::route('dashboard');
        }
        if (request()->user()->cannot('positions.create', Position::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Positions/Create', [
        ]);
    }

    public function edit($id)
    {
        $client_id = request()->user()->currentClientId();
        if (! $client_id) {
            return Redirect::route('dashboard');
        }
        if (! $id) {
            Alert::error("No Security Role ID provided")->flash();

            return Redirect::back();
        }
        if (request()->user()->cannot('positions.update', Position::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Positions/Edit', [
            'position' => Position::findOrFail($id),
        ]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        if (! $client_id) {
            abort(403);
        }
        if (request()->user()->cannot('positions.read', Position::class)) {
            abort(403);
        }

        $positions = Position::whereClientId($client_id)->get();

        return $positions;
    }
}
