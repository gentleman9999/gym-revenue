<?php

declare(strict_types=1);

namespace App\Policies;

use App\Domain\Clients\Projections\Client;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     */
    public function viewAny(User $user): mixed
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     */
    public function view(User $user, Team $team): mixed
    {
        return $user->belongsToTeam($team);
    }

    /**
     * Determine whether the user can create models.
     *
     */
    public function create(User $user): mixed
    {
        $can_create_new_teams_for_current_client = $user->can('create-new-teams', Client::find($user->client_id));

        return $user->isAccountOwner() || $user->isAdmin() || $can_create_new_teams_for_current_client;
    }

    /**
     * Determine whether the user can update the model.
     *
     */
    public function update(User $user, Team $team): mixed
    {
//        return $user->ownsTeam($team);
        $can_update_teams_for_current_client = $user->can('create-new-teams', Client::find($user->client_id));

        return $user->isAccountOwner() || $user->isAdmin() || $can_update_teams_for_current_client;
    }

    /**
     * Determine whether the user can add team members.
     *
     */
    public function addTeamMember(User $user, Team $team): mixed
    {
//        return $user->ownsTeam($team);
        $can_update_teams_for_current_client = $user->can('create-new-teams', Client::find($user->client_id));

        return $user->isAccountOwner() || $user->isAdmin() || $can_update_teams_for_current_client;
    }

    /**
     * Determine whether the user can update team member permissions.
     *
     */
    public function updateTeamMember(User $user, Team $team): mixed
    {
//        return $user->ownsTeam($team);
        $can_update_teams_for_current_client = $user->can('create-new-teams', Client::find($user->client_id));

        return $user->isAccountOwner() || $user->isAdmin() || $can_update_teams_for_current_client;
    }

    /**
     * Determine whether the user can remove team members.
     *
     */
    public function removeTeamMember(User $user, Team $team): mixed
    {
//        return $user->ownsTeam($team);
        $can_remove_team_members_from_current_client = $user->can('create-new-teams', Client::find($user->client_id));

        return $user->isAccountOwner() || $user->isAdmin() || $can_remove_team_members_from_current_client;
    }

    /**
     * Determine whether the user can delete the model.
     *
     */
    public function delete(User $user, Team $team): mixed
    {
//        return $user->ownsTeam($team);
        $can_delete_team_from_current_client = $user->can('create-new-teams', Client::find($user->client_id));

        return $user->isAccountOwner() || $user->isAdmin() || $can_delete_team_from_current_client;
    }
}
