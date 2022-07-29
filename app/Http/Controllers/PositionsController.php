<?php

namespace App\Http\Controllers;

use App\Domain\Departments\Department;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class PositionsController extends Controller
{
    public function index(Request $request)
    {
        $client_id = $request->user()->client_id;
        if (! $client_id) {
            return Redirect::route('dashboard');
        }
        if (request()->user()->cannot('positions.read', Position::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $pos = Position::whereClientId($client_id)
            ->with('departments')
            ->filter($request->only('search', 'trashed'))
            ->sort()
            ->paginate(10)
            ->appends(request()->except('page'));

        return Inertia::render('Positions/Show', [
            'positions' => $pos,
            'departments' => Department::all(),
            'filters' => $request->all('search', 'trashed', 'state'),
        ]);
    }

    public function create()
    {
        $client_id = request()->user()->client_id;
        if (! $client_id) {
            return Redirect::route('dashboard');
        }
        if (request()->user()->cannot('positions.create', Position::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Positions/Create', [
            'departments' => Department::all(),
        ]);
    }

    public function edit(Position $position)
    {
        if (request()->user()->cannot('positions.update', Position::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $position = Position::whereId($position->id)->with('departments')->first();

        return Inertia::render('Positions/Edit', [
            'position' => $position,
            'departments' => Department::all(),
        ]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $client_id = $request->user()->client_id;
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
