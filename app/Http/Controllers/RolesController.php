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

class RolesController extends Controller
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

        $roles = Role::paginate(10);

        return Inertia::render('Roles/Show', [
            'roles' => $roles,
            'filters' => $request->all('search', 'trashed', 'state')
        ]);
    }

    public function create()
    {
        $client_id = request()->user()->currentClientId();
        if (!$client_id) {
            return Redirect::route('dashboard');
        }

        return Inertia::render('Roles/Create', [
            'availableAbilities' => Bouncer::ability()->whereEntityId(null)->get(['name', 'title', 'id'])
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

        return Inertia::render('Roles/Edit', [
            'availableAbilities' => Bouncer::ability()->whereEntityId(null)->get(['name', 'title', 'id']),
            'role' => Role::findOrFail($id),
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate($this->rules);
        $current_user = $request->user();
        $client_id = $current_user->currentClientId();
        $data['client_id'] = $client_id;

        ClientAggregate::retrieve($client_id)->createRole($current_user->id, $data)->persist();

        Alert::success("Security Role '{$data['name']}' was created")->flash();

        return Redirect::route('roles');
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::route('roles');
        }

        $data = $request->validate($this->rules);
        $data['id'] = $id;

        $current_user = $request->user();
        $client_id = $current_user->currentClientId();

        ClientAggregate::retrieve($client_id)->updateRole($current_user->id, $data)->persist();

        Alert::success("Role '{$data['name']}' updated")->flash();


        return Redirect::back();
    }

    public function trash($id)
    {
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::route('roles');
        }

        $current_user = request()->user();
        $client_id = $current_user->currentClientId();
        ClientAggregate::retrieve($client_id)->trashRole($current_user->id, $id)->persist();

        Alert::success("Role trashed")->flash();
        return Redirect::back();
    }

    public function restore(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::back();
        }

        $current_user = request()->user();
        $client_id = $current_user->currentClientId();
        ClientAggregate::retrieve($client_id)->restoreRole($current_user->id, $id)->persist();

        Alert::success("Role restored")->flash();

        return Redirect::back();
    }

    public function delete($id)
    {
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::route('roles');
        }

        $current_user = request()->user();
        $client_id = $current_user->currentClientId();
        ClientAggregate::retrieve($client_id)->deleteRole($current_user->id, $id)->persist();

        Alert::success("Role trashed")->flash();
        return Redirect::back();
    }
}
