<?php

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
     * @param \App\Domain\Users\Models\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Domain\Users\Models\User $user
     * @param \App\Domain\Teams\Models\Team $team
     * @return mixed
     */
    public function view(User $user, Team $team)
    {
        return $user->belongsToTeam($team);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Domain\Users\Models\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        $can_create_new_teams_for_current_client = $user->can('create-new-teams', Client::find($user->client_id));

        return $user->isAccountOwner() || $user->isAdmin() || $can_create_new_teams_for_current_client;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Domain\Users\Models\User $user
     * @param \App\Domain\Teams\Models\Team $team
     * @return mixed
     */
    public function update(User $user, Team $team)
    {
//        return $user->ownsTeam($team);
        $can_update_teams_for_current_client = $user->can('create-new-teams', Client::find($user->client_id));

        return $user->isAccountOwner() || $user->isAdmin() || $can_update_teams_for_current_client;
    }

    /**
     * Determine whether the user can add team members.
     *
     * @param \App\Domain\Users\Models\User $user
     * @param \App\Domain\Teams\Models\Team $team
     * @return mixed
     */
    public function addTeamMember(User $user, Team $team)
    {
//        return $user->ownsTeam($team);
        $can_update_teams_for_current_client = $user->can('create-new-teams', Client::find($user->client_id));

        return $user->isAccountOwner() || $user->isAdmin() || $can_update_teams_for_current_client;
    }

    /**
     * Determine whether the user can update team member permissions.
     *
     * @param \App\Domain\Users\Models\User $user
     * @param \App\Domain\Teams\Models\Team $team
     * @return mixed
     */
    public function updateTeamMember(User $user, Team $team)
    {
//        return $user->ownsTeam($team);
        $can_update_teams_for_current_client = $user->can('create-new-teams', Client::find($user->client_id));

        return $user->isAccountOwner() || $user->isAdmin() || $can_update_teams_for_current_client;
    }

    /**
     * Determine whether the user can remove team members.
     *
     * @param \App\Domain\Users\Models\User $user
     * @param \App\Domain\Teams\Models\Team $team
     * @return mixed
     */
    public function removeTeamMember(User $user, Team $team)
    {
//        return $user->ownsTeam($team);
        $can_remove_team_members_from_current_client = $user->can('create-new-teams', Client::find($user->client_id));

        return $user->isAccountOwner() || $user->isAdmin() || $can_remove_team_members_from_current_client;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Domain\Users\Models\User $user
     * @param \App\Domain\Teams\Models\Team $team
     * @return mixed
     */
    public function delete(User $user, Team $team)
    {
//        return $user->ownsTeam($team);
        $can_delete_team_from_current_client = $user->can('create-new-teams', Client::find($user->client_id));

        return $user->isAccountOwner() || $user->isAdmin() || $can_delete_team_from_current_client;
    }
}
