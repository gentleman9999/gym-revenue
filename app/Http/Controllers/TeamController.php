<?php

namespace App\Http\Controllers;

use App\Models\Clients\Client;
use App\Models\Clients\Location;
use App\Models\Team;
use App\Models\TeamDetail;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Laravel\Jetstream\Actions\ValidateTeamDeletion;
use Laravel\Jetstream\Contracts\CreatesTeams;
use Laravel\Jetstream\Contracts\DeletesTeams;
use Laravel\Jetstream\Contracts\UpdatesTeamNames;
use Laravel\Jetstream\Jetstream;
use Prologue\Alerts\Facades\Alert;

class TeamController extends Controller
{
    protected $rules = [
        'name' => ['required', 'max:50'],
        'user_id' => ['sometimes', 'exists:users,id'],
        'personal_team' => ['sometimes', 'boolean'],
        'locations' => ['sometimes', 'array'],
    ];

    public function index(Request $request)
    {
        $current_user = $request->user();
        $client_id = $current_user->currentClientId();
        if ($client_id) {
            $client = Client::with('teams')->find($client_id);
            $team_ids = $client->teams()->pluck('value');
            $teams = Team::whereIn('id', $team_ids)->filter($request->only('search', 'club', 'team'))
                ->paginate(10);
            return Inertia::render('Teams/List', [
                'teams' => Team::filter($request->only('search', 'club', 'team'))
                    ->paginate(10),
                'filters' => $request->all('search', 'club', 'team'),
                'clubs' => Location::whereClientId($client_id)->get(),
                'teams' => $teams
            ]);
        } else if ($current_user->isCapeAndBayUser()) {
            $current_team = $current_user->currentTeam()->first();
            $teams = Team::find($current_team->id)->filter($request->only('search', 'club', 'team'))
                ->paginate(10);
            return Inertia::render('Teams/List', [
                'teams' => Team::filter($request->only('search', 'club', 'team'))
                    ->paginate(10),
                'filters' => $request->all('search', 'club', 'team'),
                'clubs' => [],
                'teams' => $teams
            ]);
        }


        $teams = Team::whereIn('id', $team_ids)->filter($request->only('search', 'club', 'team'))
            ->paginate(10);

        return Inertia::render('Teams/List', [
//            'teams' => Team::filter($request->only('search', 'club', 'team'))
//                ->paginate(10),
            'teams' => Team::filter($request->only('search', 'club', 'team'))
                ->paginate(10),
            'filters' => $request->all('search', 'club', 'team'),
            'clubs' => Location::whereClientId($client_id)->get(),
            'users' => User::whereClientId($client_id),
            'teams' => $teams
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
        $team = Jetstream::newTeamModel()->with('details')->findOrFail($teamId);
//        Gate::authorize('view', $team);

        $current_user = $request->user();

        $client_id = $current_user->currentClientId();
        $current_team = $current_user->currentTeam()->first();


        $availableUsers = [];
        $availableLocations = [];
        $users = User::whereHas('teams', function ($query) use ($current_team) {
            return $query->where('teams.id', '=', $current_team->id);
        })->get();
        if ($client_id) {
            $availableUsers = User::whereHas('detail', function ($query) use ($client_id) {
                return $query->whereName('associated_client')->whereValue($client_id);
            })->get();
            if ($current_user->isCapeAndBayUser()) {
                //if cape and bay user, add all the non client associated capeandbay users
                $availableUsers = $availableUsers->merge(User::whereDoesntHave('details', function ($query) use ($current_user) {
                    return $query->where('name', '=', 'associated-client');
                })->where('email', 'like', '%@capeandbay.com')->get());
            }
            $availableLocations = Location::whereClientId($client_id)->get();
        } else if ($current_user->isCapeAndBayUser()) {
            //look for users that aren't client users
            $availableUsers = User::whereDoesntHave('details', function ($query) use ($current_user) {
                return $query->where('name', '=', 'associated-client');
            })->where('email', 'like', '%@capeandbay.com')->get();

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

//TODO:event sourcing
    public function store(Request $request)
    {
        $data = $request->validate($this->rules);
        $creator = app(CreatesTeams::class);

        $team = $creator->create($request->user(), $data);
//        $team = Team::create($data);
        //associate locations
        foreach ($data['locations'] as $location_gymrevenue_id) {
            TeamDetail::create(['team_id' => $team->id, 'name' => 'team-location', 'value' => $location_gymrevenue_id]);
        }
        Alert::success("Team '{$team->name}' was created")->flash();

//        return Redirect::route('teams');
        return Redirect::route('teams.edit', $team->id);
    }

    public function update(Request $request, $id)
    {
        if (!$id) {
            Alert::error("No Team ID provided")->flash();
            return Redirect::route('teams');
        }
        $team = Jetstream::newTeamModel()->findOrFail($id);
        $data = $request->validate($this->rules);
        app(UpdatesTeamNames::class)->update($request->user(), $team, $data);


        TeamDetail::whereTeamId($id)->whereName('team-location')->delete();
        foreach ($data['locations'] as $location_gymrevenue_id) {
            TeamDetail::create(['team_id' => $id, 'name' => 'team-location', 'value' => $location_gymrevenue_id]);
        }

        Alert::success("Team '{$team->name}' updated")->flash();

//        return Redirect::route('teams');
        return Redirect::back();
    }

    public function delete($id)
    {
        if (!$id) {
            Alert::error("No Team ID provided")->flash();
            return Redirect::route('teams');
        }

        $team = Jetstream::newTeamModel()->findOrFail($id);

        app(ValidateTeamDeletion::class)->validate(request()->user(), $team);

        $deleter = app(DeletesTeams::class);

        $deleter->delete($team);;

        Alert::success("Team '{$team->name}' deleted")->flash();

        return Redirect::back();
    }

    public function view($teamId)
    {
        $current_team = Team::find($teamId);
        $team_users = $current_team->team_users()->get();

        if (request()->user()->isCapeAndBayUser()) {
            $data['users'] = $team_users;
        } else {
            $temp_users = [];
            foreach ($team_users as $team_user)
            {
                if($team_user->role !== 'Admin') $temp_users[] = $team_user;
            }
            $data['users'] = $temp_users;
        }

        $data['team'] = $current_team;
        $data['clubs'] = Location::whereClientId(request()->user()->currentClientId())->get();
        return $data;
    }
}
