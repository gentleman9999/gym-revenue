<?php

namespace App\Http\Controllers;

use App\Domain\Clients\Models\Client;
use App\Domain\Departments\Department;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\Models\TeamUser;
use App\Domain\Users\Models\User;
use App\Models\Clients\Location;
use App\Models\Position;
use App\Models\ReadReceipt;
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
        $locations = [];
        $teams = [];
        $clientName = 'Cape & Bay/GymRevenue';
        $filterKeys = ['search', 'club', 'team', 'roles',];

        //Populating Role Filter
        $team_users = User::with(['teams', 'home_location', 'roles'])->get();
        $roles = Role::whereScope($client_id)->get();

        if ($client_id) {
            $current_team = $request->user()->currentTeam()->first();
            $client = Client::find($client_id);

            $is_default_team = $client->default_team_id == $current_team->id;

            $locations = Location::whereClientId($client_id)->get();
            $teams = Team::findMany(Client::with('teams')->find($client_id)->teams->pluck('value'));
            $clientName = $client->name;

            // If the active team is a client's-default team get all members
            if ($is_default_team) {
                $users = User::with(['teams', 'home_location'])
                    ->filter($request->only($filterKeys))->sort()
                    ->paginate(10)
                    ->appends(request()->except('page'));
            } else {
                // else - get the members of that team
                $team_users = TeamUser::whereTeamId($current_team->id)->get();
                $user_ids = [];
                foreach ($team_users as $team_user) {
                    $user_ids[] = $team_user->user_id;
                }
                $users = User::whereIn('users.id', $user_ids)
                    ->with(['teams', 'home_location'])
                    ->filter($request->only($filterKeys))
                    ->sort()
                    ->paginate(10)
                    ->appends(request()->except('page'));
            }

            foreach ($users as $idx => $user) {
                if ($user->getRole()) {
                    $users[$idx]->role = $user->getRole();
                }

                $default_team_detail = $user->default_team()->first();
                $default_team = Team::find($default_team_detail->value);
                $users[$idx]->home_team = $default_team->name;
            }
        } else {
            //cb team selected
            $users = User::with('home_location')->whereHas('teams', function ($query) use ($request) {
                return $query->where('teams.id', '=', $request->user()->currentTeam()->first()->id);
            })->filter($request->only($filterKeys))->sort()
                ->paginate(10)->appends(request()->except('page'));

            foreach ($users as $idx => $user) {
                $users[$idx]->role = $user->getRole();
                $default_team_detail = $user->default_team()->first();
                $default_team = Team::find($default_team_detail->value);
                $users[$idx]->home_team = $default_team->name;
            }
        }

        //THIS DOESN'T WORK BECAUSE OF PAGINATION BUT IT MAKES IT LOOK LIKE IT'S WORKING FOR NOW
        //MUST FIX BY DEMO 6/15/22
        //THIS BLOCK HAS TO BE REMOVED & QUERIES REWRITTEN WITH JOINS SO ACTUAL SORTING WORKS WITH PAGINATION
        if ($request->get('sort') != '') {
            if ($request->get('dir') == 'DESC') {
                $sortedResult = $users->getCollection()->sortByDesc($request->get('sort'))->values();
            } else {
                $sortedResult = $users->getCollection()->sortBy($request->get('sort'))->values();
            }
            $users->setCollection($sortedResult);
        }

        return Inertia::render('Users/Show', [
            'users' => $users,
            'filters' => $request->all($filterKeys),
            'clubs' => $locations,
            'teams' => $teams,
            'clientName' => $clientName,
            'potentialRoles' => $roles,
        ]);
    }

    public function create(Request $request)
    {
        // Get the logged-in user making the request
        $user = request()->user();
        // Get the user's currently accessed team for scoping
        $current_team = $user->currentTeam()->first();
        // Get the first record linked to the client in client_details, this is how we get what client we're assoc'd with
        // CnB Client-based data is not present in the DB and thus the details could be empty.
        $client = $current_team->client;
        // IF we got details, we got the client name, otherwise its Cape & Bay
        $client_name = (! is_null($client)) ? $client->name : 'Cape & Bay';

        $client_id = request()->user()->currentClientId();

        // The logged in user needs the ability to create users scoped to the current team to continue
        if ($user->cannot('users.create', User::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $locations = null;
        if ($client) {
            $locations = Location::whereClientId($client->id)->get(['name', 'gymrevenue_id']);
        }

        $roles = Role::whereScope($client_id)->get();

        // Take the data and pass it to the view.
        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'clientName' => $client_name,
            'locations' => $locations,
            'availablePositions' => Position::whereClientId($client_id)->select('id', 'name')->get(),
            'availableDepartments' => Department::whereClientId($client_id)->select('id', 'name')->get(),
        ]);
    }

    public function edit(User $user)
    {
        $me = request()->user();

        $client_id = request()->user()->currentClientId();

        if ($me->cannot('users.update', User::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $user->load('details', 'notes', 'files', 'contact_preference', 'positions', 'departments');//TODO:get rid of loading all details here.

        if ($me->id == $user->id) {
            return Redirect::route('profile.show');
        }

        $roles = Role::whereScope($client_id)->get();

        $locations = null;
        if ($user->isClientUser()) {
            $locations = Location::whereClientId($user->client->id)->get(['name', 'gymrevenue_id']);
        };
        //for some reason inertiajs converts "notes" key to empty string.
        //so we set all_notes
        $userData = $user->toArray();
        $userData['all_notes'] = $user->notes->toArray();
        foreach ($userData['all_notes'] as $key => $value) {
            if (ReadReceipt::whereNoteId($userData['all_notes'][$key]['id'])->first()) {
                $userData['all_notes'][$key]['read'] = true;
            } else {
                $userData['all_notes'][$key]['read'] = false;
            }
        }
//        dd($userData);
        $userData['role_id'] = $user->role()->id;

        return Inertia::render('Users/Edit', [
            'selectedUser' => $userData,
            'roles' => $roles,
            'locations' => $locations,
            'availablePositions' => Position::whereClientId($client_id)->select('id', 'name')->get(),
            'availableDepartments' => Department::whereClientId($client_id)->select('id', 'name')->get(),
        ]);
    }

    public function view(User $user)
    {
        $requesting_user = request()->user(); //Who's driving
        if ($requesting_user->cannot('users.read', User::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }
        $user->load('details', 'teams', 'files');//TODO: get rid of loading all details here.
        $user_teams = $user->teams ?? [];
        $data = $user->toArray();
        $data['role'] = $user->getRole();

        $requesting_user_teams = $requesting_user->teams ?? [];
        $data['teams'] = $user_teams->filter(function ($user_team) use ($requesting_user_teams) {
            //only return teams that the current user also has access to
            return $requesting_user_teams->contains(function ($requesting_user_team) use ($user_team) {
                return $requesting_user_team->id === $user_team->id;
            });
        });

        return $data;
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        // Check the client ID to determine if we are in Client or Cape & Bay space
        $client_id = $request->user()->currentClientId();

        //Default Render VARs
        $filterKeys = ['search', 'club', 'team', 'roles'];


        if ($client_id) {
            $current_team = $request->user()->currentTeam()->first();
            $client = Client::find($client_id);

            $is_default_team = $client->home_team_id === $current_team->id;
            // If the active team is a client's-default team get all members
            if ($is_default_team) {
                $users = User::with(['teams'])
                    ->filter($request->only($filterKeys))
                    ->get();
            } else {
                // else - get the members of that team
                $team_users = TeamUser::whereTeamId($current_team->id)->get();
                $user_ids = [];
                foreach ($team_users as $team_user) {
                    $user_ids[] = $team_user->user_id;
                }
                $users = User::whereIn('users.id', $user_ids)
                    ->with(['teams'])
                    ->filter($request->only($filterKeys))
                    ->get();
            }

            foreach ($users as $idx => $user) {
                if ($user->getRole()) {
                    $users[$idx]->role = $user->getRole();
                }

                $default_team_detail = $user->default_team()->first();
                $default_team = Team::find($default_team_detail->value);
                $users[$idx]->home_team = $default_team->name;

                //This is phil's fault
                if (! is_null($users[$idx]->home_location_id)) {
                    $users[$idx]->home_club_name = $users[$idx]->home_club ? Location::whereGymrevenueId($users[$idx]->home_location_id)->first()->name : null;
                }
            }
        } else {
            //cb team selected
            $users = User::whereHas('teams', function ($query) use ($request) {
                return $query->where('teams.id', '=', $request->user()->currentTeam()->first()->id);
            })->filter($request->only($filterKeys))
                ->get();

            foreach ($users as $idx => $user) {
                $users[$idx]->role = $user->getRole();
                $default_team_detail = $user->default_team()->first();
                $default_team = Team::find($default_team_detail->value);
                $users[$idx]->home_team = $default_team->name;
            }
        }

        return $users;
    }
}
