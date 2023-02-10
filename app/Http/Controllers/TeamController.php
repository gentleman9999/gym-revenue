<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\Employee;
use App\Enums\SecurityGroupEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Laravel\Jetstream\Jetstream;
use Prologue\Alerts\Facades\Alert;

class TeamController extends Controller
{
    public function index(Request $request): InertiaResponse
    {
        return Inertia::render('Teams/List', [
            'filters'        => $request->all('search', 'club', 'team', 'users'),
            'clubs'          => Location::all(),
            'preview'        => $request->preview ?? null,
            'potentialUsers' => Employee::with(['teams', 'home_location'])->get(),
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('Teams/Create', [
            'availableRoles'       => array_values(Jetstream::$roles),
            'availableLocations'   => Location::all(),
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions'   => Jetstream::$defaultPermissions,
        ]);
    }

    /**
     * Show the team management screen.
     *
     * @param Request $request
     * @param Team    $team
     *
     */
    public function edit(Request $request, Team $team): InertiaResponse|RedirectResponse
    {
        if (request()->user()->cannot('teams.update', Team::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $current_user        = $request->user();
        $client_id           = $current_user->client_id;
        $available_locations = [];
        $users               = $team->users;
        $available_users     = Employee::get();

        if ($client_id) {
            if ($current_user->is_cape_and_bay_user) {
                //if cape and bay user, add all the non client associated capeandbay users
                $available_users = $available_users->merge(Employee::whereClientId(null)->get());
            }
            $available_locations = $team->home_team ? [] : Location::all();
        } elseif ($current_user->is_cape_and_bay_user) {
            //look for users that aren't client users
        }

        return Jetstream::inertia()->render($request, 'Teams/Edit', [
            'team'                 => $team->load('owner', 'users', 'teamInvitations'),
            'availableRoles'       => array_values(Jetstream::$roles),
            'availableLocations'   => $available_locations,
            'availableUsers'       => $available_users,
            'users'                => $users,
            'availablePermissions' => Jetstream::$permissions,
            'defaultPermissions'   => Jetstream::$defaultPermissions,
            'locations'            => $team->locations(),
            'permissions'          => [
                'canAddTeamMembers'    => Gate::check('addTeamMember', $team),
                'canDeleteTeam'        => Gate::check('delete', $team),
                'canRemoveTeamMembers' => Gate::check('removeTeamMember', $team),
                'canUpdateTeam'        => Gate::check('update', $team),
            ],
        ]);
    }

    /**
     * @param Team $team
     *
     * @return array<string, mixed>|RedirectResponse
     */
    public function view(Team $team): array|RedirectResponse
    {
        if (request()->user()->cannot('teams.read', Team::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $data = ['team' => $team];

        $team_users = $team->team_users()->with('user.roles')->get();

        $non_admin_users = [];
        foreach ($team_users as $team_user) {
            if ($team_user->user->securityGroup() !== SecurityGroupEnum::ADMIN && ! $team_user->is_cape_and_bay_user) {
                $non_admin_users[] = $team_user;
            }
        }

        if (count($non_admin_users) > 0) {
            $first_user     = Employee::find($non_admin_users[0]->user_id);
            $data['clubs']  = Location::all();
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
    public function export(Request $request): Team
    {
        return Team::where('client_id', $request->user()->client_id)->get();
    }
}
