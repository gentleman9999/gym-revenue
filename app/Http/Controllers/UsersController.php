<?php

namespace App\Http\Controllers;

use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class UsersController extends Controller
{
    protected $rules = [
        'name' => ['required', 'max:50'],
    ];

    public function index(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        return Inertia::render('Users/Show', [
            'users' => User::whereHas('detail', function ($query) use ($client_id) {
                return $query->whereName('associated_client')->whereValue($client_id);
            })
                ->filter($request->only('search', 'club', 'team'))
                ->paginate(10),
            'filters' => $request->all('search', 'club', 'team'),
            'clubs' => Location::whereClientId($client_id)->get(),
            'teams' => Team::findMany(Client::with('teams')->find($client_id)->teams->pluck('value'))
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Users/Create', [
        ]);
    }

    public function edit($id)
    {
        return Inertia::render('Users/Edit', [
            'selectedUser' => User::find($id)
        ]);
    }

    public function store(Request $request)
    {
        $user = User::create(
            $request->validate($this->rules)
        );
        Alert::success("User '{$user->name}' was created")->flash();

        return Redirect::route('users');
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No User ID provided")->flash();
            return Redirect::route('users');
        }

        $user = User::findOrFail($id);
        $user->updateOrFail($request->validate($this->rules));
        Alert::success("User '{$user->name}' updated")->flash();

        return Redirect::route('users');
    }

    public function delete($id)
    {
        if (!$id) {
            Alert::error("No User ID provided")->flash();
            return Redirect::route('users');
        }

        $user = User::findOrFail($id);

        $success = $user->deleteOrFail();

        Alert::success("User '{$user->name}' deleted")->flash();

        return Redirect::back();
    }
}
