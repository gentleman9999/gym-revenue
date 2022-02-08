<?php

namespace App\Http\Controllers;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Security\SecurityRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Laravel\Jetstream\Role;
use Prologue\Alerts\Facades\Alert;
use Bouncer;

class SecurityRolesController extends Controller
{
    protected $rules = [
        'security_role' => ['string', 'required'],
        'role_id' => ['integer', 'required'],
        'ability_ids' => ['array', 'sometimes'],
        'ability_ids.*' => ['integer', 'required'],
    ];

    public function index(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        if (!$client_id) {
            //not implemented for CNB users yet
            return Redirect::route('dashboard');
        }
        $securityRoles = SecurityRole::whereClientId($client_id)->whereActive(1)->filter($request->only('search', 'trashed'))
            ->paginate(10);

        return Inertia::render('SecurityRoles/Show', [
            'securityRoles' => $securityRoles,
            'filters' => $request->all('search', 'trashed', 'state')
        ]);
    }

    public function create()
    {
        $client_id = request()->user()->currentClientId();
        if (!$client_id) {
            //not implemented for CNB users yet
            return Redirect::route('dashboard');
        }
        return Inertia::render('SecurityRoles/Create', [
            'availableRoles' => Bouncer::role()::whereNotIn('name', ['Account Owner', 'Admin'])->get(['name', 'title', 'id']),
            'availableAbilities' => Bouncer::ability()->whereEntityId(null)->get(['name', 'title', 'id'])
        ]);
    }

    public function edit($id)
    {
        $client_id = request()->user()->currentClientId();
        if (!$client_id) {
            //not implemented for CNB users yet
            return Redirect::route('dashboard');
        }
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::back();
        }

        return Inertia::render('SecurityRoles/Edit', [
            'availableRoles' => Bouncer::role()::where('name', '!=', 'Account Owner')->get(['name', 'title', 'id']),
            'availableAbilities' => Bouncer::ability()->whereEntityId(null)->get(['name', 'title', 'id']),
            'securityRole' => SecurityRole::findOrFail($id),
        ]);
    }


    public function store(Request $request)
    {
        $data = $request->validate($this->rules);
        $current_user = $request->user();
        $client_id = $current_user->currentClientId();
        $data['client_id'] = $client_id;

        ClientAggregate::retrieve($client_id)->createSecurityRole($current_user->id, $data)->persist();

        Alert::success("Security Role '{$data['security_role']}' was created")->flash();

        return Redirect::route('security-roles');
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::route('security-roles');
        }

        $data = $request->validate($this->rules);
        $data['id'] = $id;

        $current_user = $request->user();
        $client_id = $current_user->currentClientId();

        ClientAggregate::retrieve($client_id)->updateSecurityRole($current_user->id, $data)->persist();

        Alert::success("Security Role '{$data['security_role']}' updated")->flash();

//        return Redirect::route('security-roles');
        return Redirect::back();
    }

    public function trash($id)
    {
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::route('security-roles');
        }

        $current_user = request()->user();
        $client_id = $current_user->currentClientId();
        ClientAggregate::retrieve($client_id)->trashSecurityRole($current_user->id, $id)->persist();

        Alert::success("Security Role trashed")->flash();
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
        ClientAggregate::retrieve($client_id)->restoreSecurityRole($current_user->id, $id)->persist();

        Alert::success("Security Role restored")->flash();

        return Redirect::back();
    }

    public function delete($id)
    {
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::route('security-roles');
        }

        $current_user = request()->user();
        $client_id = $current_user->currentClientId();
        ClientAggregate::retrieve($client_id)->deleteSecurityRole($current_user->id, $id)->persist();

        Alert::success("Security Role trashed")->flash();
        return Redirect::back();
    }
}
