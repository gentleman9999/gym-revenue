<?php

namespace App\Http\Controllers;

use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class TeamsController extends Controller
{
    protected $rules = [
        'name' => ['required', 'max:50'],
        'user_id' => ['sometimes', 'exists:users,id'],
        'personal_team' => ['sometimes', 'boolean']
    ];

    public function index(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        return Inertia::render('Teams/List', [
            'teams' => Team::filter($request->only('search', 'club', 'team'))
                ->paginate(10),
            'filters' => $request->all('search', 'club', 'team'),
            'clubs' => Location::whereClientId($client_id)->get(),
            'users' => User::whereClientId($client_id)
            //'teams' => Team::findMany(Client::with('teams')->find($client_id)->teams->pluck('value'))
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Teams/Create', [
        ]);
    }

    public function edit($id)
    {
        return Inertia::render('Teams/Edit', [
            'selectedTeam' => Team::find($id)
        ]);
    }

    public function store(Request $request)
    {
        $team = Team::create(
            $request->validate($this->rules)
        );
        $team->users()->attach(
            $request->user(), ['role' => 'Admin']
        );
        Alert::success("Team '{$team->name}' was created")->flash();

        return Redirect::route('teams');
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No User ID provided")->flash();
            return Redirect::route('teams');
        }

        $team = User::findOrFail($id);
        $team->updateOrFail($request->validate($this->rules));
        Alert::success("Team '{$team->name}' updated")->flash();

        return Redirect::route('teams');
    }

    public function delete($id)
    {
        if (!$id) {
            Alert::error("No User ID provided")->flash();
            return Redirect::route('teams');
        }

        $team = Team::findOrFail($id);

        $success = $team->deleteOrFail();

        Alert::success("Team '{$team->name}' deleted")->flash();

        return Redirect::back();
    }
}
