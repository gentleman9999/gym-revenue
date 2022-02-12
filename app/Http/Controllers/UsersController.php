<?php

namespace App\Http\Controllers;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Clients\Security\SecurityRole;
use App\Models\Team;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class UsersController extends Controller
{
    protected $rules = [
        'name' => ['required', 'max:50'],
        'email' => ['required', 'email'],
        'phone' => ['required', 'digits:10'],
        'client_id' => ['sometimes', 'exists:clients,id'],
        'security_role' => ['nullable', 'exists:security_roles,id']
        // 'security_role' => ['required_with,client_id', 'exists:security_roles,id']
    ];

    public function index(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        if ($client_id) {
            return Inertia::render('Users/Show', [
                'users' => User::with('teams')->whereHas('detail', function ($query) use ($client_id) {
                    return $query->whereName('associated_client')->whereValue($client_id);
                })->filter($request->only('search', 'club', 'team'))
                    ->paginate(10),
                'filters' => $request->all('search', 'club', 'team'),
                'clubs' => Location::whereClientId($client_id)->get(),
                'teams' => $client_id ? Team::findMany(Client::with('teams')->find($client_id)->teams->pluck('value')) : []
            ]);
        } else {
            //cb team selected
            return Inertia::render('Users/Show', [
                'users' => User::whereHas('teams', function ($query) use ($request) {
                    return $query->where('teams.id', '=', $request->user()->currentTeam()->first()->id);
                })->filter($request->only('search', 'club', 'team'))
                    ->paginate(10),
                'filters' => $request->all('search', 'club', 'team'),
                'clubs' => [],
                'teams' => []
            ]);
        }
    }

    public function create(Request $request)
    {
        $user = request()->user();
        $current_team = $user->currentTeam()->first();
        $client_detail = $current_team->client_details()->first();
        $client = (!is_null($client_detail)) ? $client_detail->client()->first() : null;
        $client_name = (!is_null($client_detail)) ? $client->name : 'Cape & Bay';
        if($user->cannot('users.create', $current_team))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        $security_roles = SecurityRole::whereActive(1)->whereClientId(request()->user()->currentClientId());
        if(!request()->user()->isAccountOwner()){
            $security_roles = $security_roles->where('security_role', '!=', 'Account Owner');
        }
        $security_roles = $security_roles->get(['id', 'security_role']);

        return Inertia::render('Users/Create', [
            'securityRoles' => $security_roles,
            'clientName' => $client_name
        ]);
    }

    public function edit($id)
    {
        $user = request()->user();
        $current_team = $user->currentTeam()->first();
        if($user->cannot('users.update', $current_team))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        $user = $user->with('details', 'phone_number')->findOrFail($id);

        $security_roles = SecurityRole::whereActive(1)->whereClientId(request()->user()->currentClientId());
        if(!$user->isAccountOwner()) {
            $security_roles = $security_roles->where('security_role', '!=', 'Account Owner');
        }
        $security_roles = $security_roles->get(['id', 'security_role']);

        return Inertia::render('Users/Edit', [
            'selectedUser' => $user,
            'securityRoles' => $security_roles
        ]);
    }

    public function store(Request $request)
    {
        $create_rules = array_merge($this->rules, ['password' => 'required', 'email' => ['required', 'email', 'unique:users,email']]);
        $data = $request->validate($create_rules);
        $data['password'] = bcrypt($data['password']);
        $user = User::create($data);
        $security_role = SecurityRole::with('role')->find($data['security_role']);
        UserDetails::create(['user_id' => $user->id, 'name' => 'security_role', 'value'=>$security_role->id]);

        $client_id = $request->user()->currentClientId();
        if ($client_id) {
            UserDetails::create(['user_id' => $user->id, 'name' => 'associated_client', 'value' => $client_id]);
        }
        $current_team = $request->user()->currentTeam()->first();
        UserDetails::create(['user_id' => $user->id, 'name' => 'default_team', 'value' => $current_team->id]);
        $role = $security_role->role->name;
        $current_team->users()->attach(
            $user, ['role' => $role]
        );

        $current_phone = $request->phone;
        UserDetails::create(['user_id' => $user->id, 'name' => 'phone', 'value' => $current_phone]);

        if ($client_id) {
            $aggy = ClientAggregate::retrieve($client_id);
            $aggy->addUserToTeam($user->id, $current_team->id, $role);
            $aggy->persist();
        }
        Alert::success("User '{$user->name}' was created")->flash();

        return Redirect::route('users');
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No User ID provided")->flash();
            return Redirect::route('users');
        }

        $data = $request->validate($this->rules);
        $current_user = $request->user();
        $user = User::findOrFail($id);

        $phone = $data['phone'];
        $phone_detail = UserDetails::firstOrCreate([
            'user_id' => $user->id,
            'name' => 'phone',
            //'value' => $phone
        ]);
        $phone_detail->value = $phone;
        $phone_detail->save();

        $user->updateOrFail($data);
        $current_team = $current_user->currentTeam()->first();
        $old_role = $current_user->teams()->get()->keyBy('id')[$current_team->id]->pivot->role;
        if($data['security_role']){
            $security_role = SecurityRole::with('role')->find($data['security_role']);
            UserDetails::firstOrCreate(['user_id' => $user->id, 'name' => 'security_role'])->updateOrFail(['value'=>$security_role->id]);
            $role = $security_role->role->name;
            $user->teams()->sync([$current_team->id => ['role' => $role]]);
        }

        $client_id = $current_user->currentClientId();
        if ($client_id && $role !== $old_role) {
            $aggy = ClientAggregate::retrieve($client_id);
            $aggy->updateUserRoleOnTeam($user->id, $current_team->id, $old_role, $role);
            $aggy->persist();
        }
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
