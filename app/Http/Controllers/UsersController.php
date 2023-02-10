<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Models\Team;
use App\Domain\Teams\Models\TeamUser;
use App\Domain\Users\Models\User;
use App\Enums\UserTypesEnum;
use App\Support\CurrentInfoRetriever;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Prologue\Alerts\Facades\Alert;
use Silber\Bouncer\Database\Role;

class UsersController extends Controller
{
    public function index(Request $request): InertiaResponse|RedirectResponse
    {
        // Check the client ID to determine if we are in Client or Cape & Bay space
        $client_id = $request->user()->client_id;

        //Default Render VARs
        $locations   = [];
        $teams       = [];
        $client_name = 'Cape & Bay/GymRevenue';
        $filter_keys = ['search', 'club', 'team', 'roles',];

        //Populating Role Filter
        $roles = Role::whereScope($client_id)->get();

        if ($client_id) {
            $client = Client::find($client_id);

            $locations   = Location::all();
            $teams       = Team::findMany(Client::with('teams')->find($client_id)->teams()->get()->pluck('id'));
            $client_name = $client->name;
        }
        //TODO: we should not need to pass anything back but filters maybe?
        return Inertia::render('Users/Show', [
            'filters' => $request->all($filter_keys),
            'clubs' => $locations,
            'teams' => $teams,
            'clientName' => $client_name,
            'potentialRoles' => $roles,
        ]);
    }

    public function create(): InertiaResponse|RedirectResponse
    {
        // Get the user's currently accessed team for scoping
        $current_team = CurrentInfoRetriever::getCurrentTeam();
        // Get the first record linked to the client in client_details, this is how we get what client we're assoc'd with
        // CnB Client-based data is not present in the DB and thus the details could be empty.
        $client = $current_team->client()->first();
        // IF we got details, we got the client name, otherwise its Cape & Bay
        $client_name = $client !== null ? $client->name : 'Cape & Bay';

        // The logged in user needs the ability to create users scoped to the current team to continue
        if (($user = request()->user())->cannot('users.create', User::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $locations = null;
        if ($client) {
            $locations = Location::get(['name', 'gymrevenue_id']);
        }

        $roles = Role::whereScope($user->client_id)->get();

        // Take the data and pass it to the view.
        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'clientName' => $client_name,
            'locations' => $locations,
        ]);
    }

    public function edit(User $user): InertiaResponse|RedirectResponse
    {
        $me        = request()->user();
        $client_id = $me->client_id;

        if ($me->cannot('users.update', User::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }

        $user->load('notes', 'files', 'contact_preference', 'emergencyContact');//TODO:get rid of loading all details here.

        if ($me->id === $user->id) {
            return Redirect::route('profile.show');
        }

        $roles = Role::whereScope($client_id)->get();

        $locations = null;
        if ($user->isClientUser()) {
            $locations = Location::get(['name', 'gymrevenue_id']);
        }

        return Inertia::render('Users/Edit', [
            'id' => $user->id,
            'roles' => $roles,
            'locations' => $locations,
            'uploadFileRoute' => 'users.files.store',
        ]);
    }

    /**
     *
     * @return array<string, mixed>|RedirectResponse
     */
    public function view(User $user): array|RedirectResponse
    {
        $requesting_user = request()->user(); //Who's driving
        if ($requesting_user->cannot('users.read', User::class)) {
            Alert::error("Oops! You dont have permissions to do that.")->flash();

            return Redirect::back();
        }
        $user->load('teams', 'files');//TODO: get rid of loading all details here.
        $user_teams   = $user->teams ?? [];
        $data         = $user->toArray();
        $data['role'] = $user->getRole();

        $requesting_user_teams = $requesting_user->teams ?? [];
        $data['teams']         = $user_teams->filter(function (Team $user_team) use ($requesting_user_teams
        ): bool {
            //only return teams that the current user also has access to
            return $requesting_user_teams->contains(
                fn(Team $requesting_user_team): bool => $requesting_user_team->id === $user_team->id
            );
        });

        return $data;
    }

    //TODO:we could do a ton of cleanup here between shared codes with index. just ran out of time.

    /**
     *
     * @return Collection<User>
     */
    public function export(Request $request): Collection
    {
        // Check the client ID to determine if we are in Client or Cape & Bay space
        $client_id = $request->user()->client_id;

        //Default Render VARs
        $filter_keys = ['search', 'club', 'team', 'roles'];


        if ($client_id !== null) {
            $current_team = CurrentInfoRetriever::getCurrentTeam();
            $client       = Client::find($client_id);

            $is_default_team = $client->home_team_id === $current_team->id;
            // If the active team is a client's-default team get all members
            if ($is_default_team) {
                $users = User::whereUserType(UserTypesEnum::EMPLOYEE)
                    ->with(['teams'])
                    ->filter($request->only($filter_keys))
                    ->get();
            } else {
                // else - get the members of that team
                $team_users = TeamUser::whereTeamId($current_team->id)->get();
                $user_ids   = [];
                foreach ($team_users as $team_user) {
                    $user_ids[] = $team_user->user_id;
                }
                $users = User::whereUserType(UserTypesEnum::EMPLOYEE)
                    ->whereIn('users.id', $user_ids)
                    ->with(['teams'])
                    ->filter($request->only($filter_keys))
                    ->get();
            }

            foreach ($users as $user) {
                if ($user->getRole()) {
                    $user->role = $user->getRole();
                }

                $user->home_team = $user->defaultTeam->name;

                //This is phil's fault
                if ($user->home_location_id !== null) {
                    $user->home_club_name = $user->home_club
                        ? Location::whereGymrevenueId($user->home_location_id)->first()->name
                        : null;
                }
            }
        } else {
            //cb team selected
            $users = User::whereUserType(UserTypesEnum::EMPLOYEE)->whereHas(
                'teams',
                fn ($query) => $query->where('teams.id', '=', CurrentInfoRetriever::getCurrentTeam()->id)
            )->filter($request->only($filter_keys))->get();

            foreach ($users as $user) {
                $user->role      = $user->getRole();
                $user->home_team = $user->defaultTeam->name;
            }
        }

        return $users;
    }
}
