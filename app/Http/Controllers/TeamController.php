<?php

namespace App\Http\Controllers;

use App\Domain\Clients\Models\Client;
use App\Domain\Teams\Models\Team;
use App\Enums\SecurityGroupEnum;
use App\Models\Clients\Location;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;
use Prologue\Alerts\Facades\Alert;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        $current_user = $request->user();
        $client_id = $current_user->currentClientId();
        //   $users = $current_team->team_users()->get();
        $users = User::with(['teams', 'home_location'])->get();

        $teams = Team::filter($request->only('search', 'club', 'team', 'users'))->sort()->paginate(10)->appends(request()->except('page'));
        $clubs = Location::whereClientId($client_id)->get();


        return Inertia::render('Teams/List', [
            'filters' => $request->all('search', 'club', 'team', 'users'),
            'clubs' => $clubs ?? null,
            'teams' => $teams ?? null,
            'preview' => $request->preview ?? null,
            'potentialUsers' => $users,
        ]);
    }

    public function create(Request $request)
    {
//        Gate::authorize('create', Jetstream::newTeamModel());
        return Inertia::render('Teams/Create', [
            'availableRoles' => array_values(Jetstream::$roles),
            'availableLocations' => Location::whereClientId($request->user()->currentClientId())->get(),
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions' => Jetstream::$defaultPermissions,
        ]);
    }

    /**
     * Show the team management screen.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $teamId
     * @return \Inertia\Response
     */
    public function edit(Request $request, $teamId)
    {
        if (request()->user()->cannot('teams.update', Team::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $team = Jetstream::newTeamModel()->with('details')->findOrFail($teamId);
//        Gate::authorize('view', $team);

        $current_user = $request->user();

        $client_id = $current_user->currentClientId();
        $current_team = $current_user->currentTeam()->first();


        $availableUsers = [];
        $availableLocations = [];
        $users = $team->users;

        $availableUsers = User::get();

        if ($client_id) {
            if ($current_user->is_cape_and_bay_user) {
                //if cape and bay user, add all the non client associated capeandbay users
                $availableUsers = $availableUsers->merge(User::whereClientId(null)->get());
            }
            $availableLocations = $team->home_team ? [] : Location::whereClientId($client_id)->get();
        } elseif ($current_user->is_cape_and_bay_user) {
            //look for users that aren't client users
        }

        return Jetstream::inertia()->render($request, 'Teams/Edit', [
            'team' => $team->load('owner', 'users', 'teamInvitations'),
            'availableRoles' => array_values(Jetstream::$roles),
            'availableLocations' => $availableLocations,
            'availableUsers' => $availableUsers,
            'users' => $users,
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions' => Jetstream::$defaultPermissions,
            'locations' => $team->locations()->get('value'),
            'permissions' => [
                'canAddTeamMembers' => Gate::check('addTeamMember', $team),
                'canDeleteTeam' => Gate::check('delete', $team),
                'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
                'canUpdateTeam' => Gate::check('update', $team),
            ],
        ]);
    }

    public function view($teamId)
    {
        if (request()->user()->cannot('teams.read', Team::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $current_team = Team::find($teamId);
        $data['team'] = $current_team;

        $team_users = $current_team->team_users()->get();
        $non_admin_users = [];
        foreach ($team_users as $team_user) {
            if ($team_user->user->securityGroup() !== SecurityGroupEnum::ADMIN && ! $team_user->is_cape_and_bay_user) {
                $non_admin_users[] = $team_user;
            }
        }

        if (count($non_admin_users) > 0) {
            $first_user = User::find($non_admin_users[0]->user_id);
            $data['clubs'] = Location::whereClientId($first_user->client->id)->get();
            $data['client'] = Client::find($first_user->client->id);
        }

        if (request()->user()->is_cape_and_bay_user) {
            $data['users'] = $team_users;
        } else {
            $data['users'] = $non_admin_users;
        }

        return $data;
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.
    public function export(Request $request)
    {
        $teams = Team::filter($request->only('search', 'club', 'team', 'users'))->sort()->paginate(10)->appends(request()->except('page'));

        return $teams;
    }
}
