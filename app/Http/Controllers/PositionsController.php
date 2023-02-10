<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Departments\Department;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Prologue\Alerts\Facades\Alert;

class PositionsController extends Controller
{
    public function index(Request $request): InertiaResponse|RedirectResponse
    {
        $m         = 2;
        $client_id = ($user = $request->user())->client_id;
        if ($client_id === null) {
            return Redirect::route('dashboard');
        }

        echo $m;


        if ($user->cannot('positions.read', Position::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Positions/Show', [
            'departments' => Department::all(),
            'filters'     => $request->all('search', 'trashed', 'state'),
        ]);
    }

    public function create(): InertiaResponse|RedirectResponse
    {
        $client_id = ($user = request()->user())->client_id;
        if ($client_id === null) {
            return Redirect::route('dashboard');
        }

        if ($user->cannot('positions.create', Position::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        return Inertia::render('Positions/Create', [
            'departments' => Department::all(),
        ]);
    }

    public function edit(Position $position): InertiaResponse|RedirectResponse
    {
        if (request()->user()->cannot('positions.update', Position::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $position = Position::whereId($position->id)->with('departments')->first();

        return Inertia::render('Positions/Edit', [
            'position'    => $position,
            'departments' => Department::all(),
        ]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.

    /**
     *
     */
    public function export(Request $request): Collection
    {
        $client_id = $request->user()->client_id;
        if ($client_id === null) {
            abort(403);
        }
        if (request()->user()->cannot('positions.read', Position::class)) {
            abort(403);
        }

        return Position::whereClientId($client_id)->get();
    }
}
