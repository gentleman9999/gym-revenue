<?php

namespace App\Http\Controllers;

use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Clients\Security\SecurityRole;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        // Check the client ID to determine if we are in Client or Cape & Bay space
        $client_id = $request->user()->currentClientId();
        if ($client_id) {
            $current_team = $request->user()->currentTeam()->first();
            $client = Client::whereId($client_id)->with('default_team_name')->first();

            $is_default_team = $client->default_team_name->value == $current_team->id;

            // If the active team is a client's-default team get all members
            if($is_default_team)
            {
                $users = User::with(['teams', 'home_club'])->whereHas('detail', function ($query) use ($client_id) {
                    return $query->whereName('associated_client')->whereValue($client_id);
                })->filter($request->only('search', 'club', 'team'))
                    ->paginate(10);
            }
            else
            {
                // else - get the members of that team
                $team_users = TeamUser::whereTeamId($current_team->id)->get();
                $user_ids = [];
                foreach($team_users as $team_user)
                {
                    $user_ids[] = $team_user->user_id;
                }
                $users = User::whereIn('id', $user_ids)
                    ->with(['teams', 'home_club'])
                    ->filter($request->only('search', 'club', 'team'))
                    ->paginate(10);
            }


            foreach($users as $idx => $user)
            {
                $role = $user->roles()->first();
                $users[$idx]->role = $role->name;
                $default_team_detail = $user->default_team()->first();
                $default_team = Team::find($default_team_detail->value);
                $users[$idx]->home_team = $default_team->name;

                $users[$idx]->home_club_name = $users[$idx]->home_club ? Location::whereGymrevenueId($users[$idx]->home_club->value)->first()->name : null;

            }

            return Inertia::render('Users/Show', [
                'users' => $users,
                'filters' => $request->all('search', 'club', 'team'),
                'clubs' => Location::whereClientId($client_id)->get(),
                'teams' => $client_id ? Team::findMany(Client::with('teams')->find($client_id)->teams->pluck('value')) : [],
                'clientName' => $client->name
            ]);
        } else {
            //cb team selected
            $users = User::whereHas('teams', function ($query) use ($request) {
                return $query->where('teams.id', '=', $request->user()->currentTeam()->first()->id);
            })->filter($request->only('search', 'club', 'team'))
                ->paginate(10);

            foreach($users as $idx => $user)
            {
                $role = $user->roles()->first();
                $users[$idx]->role = $role->name;
                $default_team_detail = $user->default_team()->first();
                $default_team = Team::find($default_team_detail->value);
                $users[$idx]->home_team = $default_team->name;
            }

            return Inertia::render('Users/Show', [
                'users' => $users,
                'filters' => $request->all('search', 'club', 'team'),
                'clubs' => [],
                'teams' => [],
                'clientName' => 'Cape & Bay/GymRevenue'
            ]);
        }
    }

    public function create(Request $request)
    {
        // Get the logged-in user making the request
        $user = request()->user();
        // Get the user's currently accessed team for scoping
        $current_team = $user->currentTeam()->first();
        // Get the first record linked to the client in client_details, this is how we get what client we're assoc'd with
        $client_detail = $current_team->client_details()->first();
        // CnB Client-based data is not present in the DB and thus the details could be empty.
        $client = (!is_null($client_detail)) ? $client_detail->client()->first() : null;
        // IF we got details, we got the client name, otherwise its Cape & Bay
        $client_name = (!is_null($client_detail)) ? $client->name : 'Cape & Bay';

        // The logged in user needs the ability to create users scoped to the current team to continue
        if($user->cannot('users.create', $current_team))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        // Query for the security roles as that's part of the form
        $security_roles = SecurityRole::whereActive(1)->whereClientId(request()->user()->currentClientId());
        if(!request()->user()->isAccountOwner()){
            $security_roles = $security_roles->where('security_role', '!=', 'Account Owner');
        }
        $security_roles = $security_roles->get(['id', 'security_role']);

        $locations = null;
        if($client){
            $locations = Location::whereClientId($client->id)->get(['name', 'gymrevenue_id']);
        }

        // Take the data and pass it to the view.
        return Inertia::render('Users/Create', [
            'securityRoles' => $security_roles,
            'clientName' => $client_name,
            'locations' => $locations,
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

        $user = $me->with([
                'details', 'phone_number', 'altEmail', 'address1', 'address2',
                'city', 'state', 'zip', 'jobTitle', 'home_club','notes', 'start_date', 'end_date', 'termination_date'
            ])->findOrFail($id);

        if($me->id == $user->id)
        {
            return Redirect::route('profile.show');
        }

        $security_roles = SecurityRole::whereActive(1)->whereClientId(request()->user()->currentClientId());
        if(!$user->isAccountOwner()) {
            $security_roles = $security_roles->where('security_role', '!=', 'Account Owner');
        }
        $security_roles = $security_roles->get(['id', 'security_role']);

        $locations = null;
        if($user->isClientUser()){
            $locations = Location::whereClientId($user->client()->first()->id)->get(['name', 'gymrevenue_id']);
        }

//dd($user);

        return Inertia::render('Users/Edit', [
            'selectedUser' => $user,
            'securityRoles' => $security_roles,
            'locations' => $locations
        ]);
    }

    public function view($id)
    {
        $requesting_user = request()->user();
        $current_team = $requesting_user->currentTeam()->first();
        if ($requesting_user->cannot('users.read', $current_team)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }
        $requesting_user_teams = $requesting_user->teams ?? [];

        $user = User::with('details', 'teams', 'phone_number')->findOrFail($id);
        $user_teams = $user->teams ?? [];

        $data = $user->toArray();
        if ($user->security_role) {
            $security_role = SecurityRole::find($user->security_role->value);
            $data['security_role'] = $security_role->security_role;
        }else{
            $data['role'] = $user->teams->keyBy('id')[$current_team->id]->pivot->role;
        }

        if ($user->phone_number) {
            $data['phone'] = $user->phone_number->value;
        }

        $data['teams'] = $user_teams->filter(function ($user_team) use ($requesting_user_teams) {
            //only return teams that the current user also has access to
            return $requesting_user_teams->contains(function ($requesting_user_team) use($user_team) {
                return $requesting_user_team->id === $user_team->id;
            });
        });

        return $data;
    }
}
