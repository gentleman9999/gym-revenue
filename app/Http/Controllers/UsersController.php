<?php

namespace App\Http\Controllers;

use App\Models\Clients\Classification;
use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Prologue\Alerts\Facades\Alert;
use Silber\Bouncer\Database\Role;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        // Check the client ID to determine if we are in Client or Cape & Bay space
        $client_id = $request->user()->currentClientId();

        //Default Render VARs
        $clubs = [];
        $teams = [];
        $clientName = 'Cape & Bay/GymRevenue';
        $filterKeys = ['search', 'club', 'team', 'roles'];

        //Populating Role Filter
        $team_users = User::with(['teams', 'home_club', 'is_manager', 'roles'])->whereHas('detail', function ($query) use ($client_id) {
            return $query->whereName('associated_client')->whereValue($client_id);
        })->get();
        $roles = [];
        foreach($team_users as $team_user)
        {
            $user_roles = $team_user->roles;
            if($user_roles->has(0)) {
                $roles[] = [
                    'id' => $user_roles[0]->id,
                    'name' => $user_roles[0]->name,
                    'title' => $user_roles[0]->title
                ];
            }
        }

        if ($client_id) {
            $current_team = $request->user()->currentTeam()->first();
            $client = Client::whereId($client_id)->with('default_team_name')->first();

            $is_default_team = $client->default_team_name->value == $current_team->id;

            $clubs = Location::whereClientId($client_id)->get();
            $teams = Team::findMany(Client::with('teams')->find($client_id)->teams->pluck('value'));
            $clientName = $client->name;

            // If the active team is a client's-default team get all members
            if($is_default_team)
            {
                $users = User::with(['teams', 'home_club', 'is_manager', 'classification'])->whereHas('detail', function ($query) use ($client_id) {
                    return $query->whereName('associated_client')->whereValue($client_id);
                })->filter($request->only($filterKeys))
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
                    ->with(['teams', 'home_club', 'is_manager', 'classification'])
                    ->filter($request->only($filterKeys))
                    ->paginate(10);
            }

            foreach($users as $idx => $user)
            {
                if($user->getRole()){
                    $users[$idx]->role = $user->getRole();
                }

                $default_team_detail = $user->default_team()->first();
                $default_team = Team::find($default_team_detail->value);
                $users[$idx]->home_team = $default_team->name;

                //redneck join to find out classification name based on ID, will probably refactor this
                if(!is_null($users[$idx]->classification->value))
                    $users[$idx]->classification->value = Classification::whereId($users[$idx]->classification->value)->first()->title;

                //This is phil's fault
                if(!is_null($users[$idx]->home_club->value))
                    $users[$idx]->home_club_name = $users[$idx]->home_club ? Location::whereGymrevenueId($users[$idx]->home_club->value)->first()->name : null;
            }
        } else {
            //cb team selected
            $users = User::with( 'is_manager')->whereHas('teams', function ($query) use ($request) {
                return $query->where('teams.id', '=', $request->user()->currentTeam()->first()->id);
            })->filter($request->only($filterKeys))
                ->paginate(10);

            foreach($users as $idx => $user)
            {
                $users[$idx]->role = $user->getRole();
                $default_team_detail = $user->default_team()->first();
                $default_team = Team::find($default_team_detail->value);
                $users[$idx]->home_team = $default_team->name;
            }
        }

        return Inertia::render('Users/Show', [
            'users' => $users,
            'filters' => $request->all($filterKeys),
            'clubs' => $clubs,
            'teams' => $teams,
            'clientName' => $clientName,
            'potentialRoles' => array_map("unserialize", array_unique(array_map("serialize", $roles))),
        ]);
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

        $client_id = request()->user()->currentClientId();

        // The logged in user needs the ability to create users scoped to the current team to continue
        if($user->cannot('users.create', User::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        $locations = null;
        if($client){
            $locations = Location::whereClientId($client->id)->get(['name', 'gymrevenue_id']);
        }

        $roles = Role::whereScope($client_id)->get();
        $classifications = Classification::whereClientId($client_id)->get();

        // Take the data and pass it to the view.
        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'classifications' => $classifications,
            'clientName' => $client_name,
            'locations' => $locations,
        ]);
    }

    public function edit($id)
    {
        $me = request()->user();

        $client_id = request()->user()->currentClientId();

        if($me->cannot('users.update', User::class))
        {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        $user = $me->with([
                'details', 'phone_number', 'altEmail', 'address1', 'address2',
                'city', 'state', 'zip', 'jobTitle', 'home_club','notes', 'start_date', 'end_date', 'termination_date',
                'files', 'classification',
            ])->findOrFail($id);

        if($me->id == $user->id)
        {
            return Redirect::route('profile.show');
        }

        $roles = Role::whereScope($client_id)->get();
        $classifications = Classification::whereClientId($client_id)->get();

        $locations = null;
        if($user->isClientUser()){
            $locations = Location::whereClientId($user->client()->first()->id)->get(['name', 'gymrevenue_id']);
        }
;
        //for some reason inertiajs converts "notes" key to empty string.
        //so we set all_notes
        $userData = $user->toArray();
        $userData['all_notes'] = $user->notes->pluck('note')->toArray();
        $userData['role_id'] = $user->role()->id;

        return Inertia::render('Users/Edit', [
            'selectedUser' => $userData,
            'roles' => $roles,
            'classifications' => $classifications,
            'locations' => $locations
        ]);
    }

    public function view($id)
    {
        $requesting_user = request()->user(); //Who's driving
        if ($requesting_user->cannot('users.read', User::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();
            return Redirect::back();
        }

        $user = User::with('details', 'teams', 'phone_number', 'files', 'classification')->findOrFail($id); //User we're peeking
        $user_teams = $user->teams ?? [];
        $data = $user->toArray();
        $data['role'] = $user->getRole();

        if(!is_null($user->classification->value))
            $data['classification']['value'] = Classification::whereId($data['classification']['value'])->first()->title;

        if ($user->phone_number) { //Not totally sure this is necessary atm
            $data['phone'] = $user->phone_number->value;
        }

        $requesting_user_teams = $requesting_user->teams ?? [];
        $data['teams'] = $user_teams->filter(function ($user_team) use ($requesting_user_teams) {
            //only return teams that the current user also has access to
            return $requesting_user_teams->contains(function ($requesting_user_team) use($user_team) {
                return $requesting_user_team->id === $user_team->id;
            });
        });

        return $data;
    }
}
