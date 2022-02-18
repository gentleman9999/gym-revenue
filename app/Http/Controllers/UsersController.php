<?php

namespace App\Http\Controllers;

use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Clients\Security\SecurityRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $client_id = $request->user()->currentClientId();
        if ($client_id) {
            $current_team = $request->user()->currentTeam()->first();
            $client_detail = $current_team->client_details()->first();
            $client = (!is_null($client_detail)) ? $client_detail->client()->first() : null;
            $client_name = $client->name;

            return Inertia::render('Users/Show', [
                'users' => User::with('teams')->whereHas('detail', function ($query) use ($client_id) {
                    return $query->whereName('associated_client')->whereValue($client_id);
                })->filter($request->only('search', 'club', 'team'))
                    ->paginate(10),
                'filters' => $request->all('search', 'club', 'team'),
                'clubs' => Location::whereClientId($client_id)->get(),
                'teams' => $client_id ? Team::findMany(Client::with('teams')->find($client_id)->teams->pluck('value')) : [],
                'clientName' => $client_name
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
                'teams' => [],
                'clientName' => 'Cape & Bay/GymRevenue'
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
        $me = request()->user();
        $current_team = $me->currentTeam()->first();
        if($me->cannot('users.update', $current_team))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        $user = $me->with('details', 'phone_number')->findOrFail($id);
        if($me->id == $user->id)
        {
            return Redirect::route('profile.show');
        }

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
}
