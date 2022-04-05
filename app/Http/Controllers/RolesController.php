<?php

namespace App\Http\Controllers;

use App\Aggregates\Clients\ClientAggregate;
use App\Enums\SecurityGroupEnum;
use App\Models\Clients\Classification;
use App\Models\Role;
use Bouncer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

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

        if($request->user()->cannot('roles.read',Role::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        $roles = Role::whereScope($client_id)->sort()->paginate(10);

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

        if(request()->user()->cannot('roles.create',Role::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        return Inertia::render('Roles/Create', [
            'availableAbilities' => Bouncer::ability()->whereEntityId(null)->get(['name', 'title', 'id']),
            'securityGroups' => collect(SecurityGroupEnum::cases())->keyBy('name')->except('ADMIN')->values()->map(function($s){return ['value' => $s->value, 'name' => $s->name];})
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
        if(request()->user()->cannot('roles.update',Role::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        $role = Role::findOrFail($id)->toArray();
        $role['abilities'] = Bouncer::role()->find($id)->getAbilities()->toArray();

        return Inertia::render('Roles/Edit', [
            'availableAbilities' => Bouncer::ability()->whereEntityId(null)->get(['name', 'title', 'id']),
            'role' => $role,
            'abilities' => Bouncer::role()->find($id)->getAbilities(),
            'securityGroups' => collect(SecurityGroupEnum::cases())->keyBy('name')->except('ADMIN')->values()->map(function($s){return ['value' => $s->value, 'name' => $s->name];})
        ]);
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        if (!$client_id) {
            return Redirect::route('dashboard');
        }

        if($request->user()->cannot('roles.read',Role::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        $roles = Role::whereScope($client_id)->get();

        return $roles;
    }


}
