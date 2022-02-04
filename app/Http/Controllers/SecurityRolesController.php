<?php

namespace App\Http\Controllers;

use App\Models\Clients\Client;
use App\Models\Clients\Security\SecurityRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Laravel\Jetstream\Role;
use Prologue\Alerts\Facades\Alert;
use Bouncer;

class SecurityRolesController extends Controller
{
    protected $rules = [];

    public function index(Request $request)
    {
        $securityRoles = SecurityRole::filter($request->only('search', 'trashed'))
            ->paginate(10);

        return Inertia::render('SecurityRoles/Show', [
            'securityRoles' => $securityRoles,
            'filters' => $request->all('search', 'trashed', 'state')
        ]);
    }

    public function create()
    {
        return Inertia::render('SecurityRoles/Create', [
            'availableRoles'=> Bouncer::role()::all(['name', 'title', 'id']),
            'availableAbilities' => Bouncer::ability()->whereEntityId(null)->get(['name', 'title', 'id'])
        ]);
    }

    public function edit($id)
    {
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::back();
        }

        return Inertia::render('SecurityRoles/Edit', [
            'availableRoles'=> Bouncer::role()::all(['name', 'title', 'id']),
            'availableAbilities' => Bouncer::ability()->whereEntityId(null)->get(['name', 'title', 'id']),
            'securityRole' => SecurityRole::find($id),
        ]);
    }


    public function store(Request $request)
    {
        $securityRole = SecurityRole::create(
            $request->validate($this->rules)
        );
        Alert::success("Security Role '{$securityRole->security_role}' was created")->flash();

        return Redirect::route('security-roles');
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::route('security-roles');
        }

        $securityRole = SecurityRole::findOrFail($id);
        $securityRole->updateOrFail($request->validate($this->rules));
        Alert::success("Security Role '{$securityRole->security_role}' updated")->flash();

        return Redirect::route('security-roles');
    }

    public function trash($id)
    {
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::route('security-roles');
        }

        $ecurityRole = SecurityRole::findOrFail($id);

        $success = $ecurityRole->deleteOrFail();

        Alert::success("Security Role '{$ecurityRole->name}' trashed")->flash();
        return Redirect::back();
    }

    public function restore(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No Security Role ID provided")->flash();
            return Redirect::back();
        }
        $securityRole = SecurityRole::withTrashed()->findOrFail($id);
        $securityRole->restore();

        Alert::success("Security Role '{$securityRole->security_role}' restored")->flash();

        return Redirect::back();
    }
}
