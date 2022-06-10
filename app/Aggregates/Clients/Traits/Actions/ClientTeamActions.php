<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\Aggregates\Users\UserAggregate;
use App\Exceptions\Clients\ClientAccountException;
use App\Models\Team;
use App\StorableEvents\Clients\CapeAndBayUsersAssociatedWithClientsNewDefaultTeam;
use App\StorableEvents\Clients\PrefixCreated;
use App\StorableEvents\Clients\TeamAttachedToClient;
use App\StorableEvents\Clients\Teams\ClientTeamDeleted;
use App\StorableEvents\Clients\Teams\ClientTeamUpdated;
use App\StorableEvents\Clients\UserRemovedFromTeam;
use App\StorableEvents\Clients\UserRoleOnTeamUpdated;

trait ClientTeamActions
{
    public function createTeamPrefix(string $prefix)
    {
        if (! empty($this->team_prefix)) {
            throw ClientAccountException::prefixAlreadyCreated($this->team_prefix, $this->home_team);
        } else {
            $this->recordThat(new PrefixCreated($this->uuid(), $prefix));
        }

        return $this;
    }

    public function attachTeamToClient(string $team, User | string | null $created_by_user = null)
    {
        if (array_key_exists($team, $this->teams)) {
            throw ClientAccountException::teamAlreadyAssigned($team);
        }
        // @todo - make sure the team is not assigned to another client

        $created_by_user_id = $created_by_user->id ?? null;

        $this->recordThat(new TeamAttachedToClient($team, $created_by_user_id));

        return $this;
    }

    public function deleteTeam()
    {
        $this->recordThat(new ClientTeamDeleted());

        return $this;
    }

    public function updateTeam(array $payload)
    {
        $this->recordThat(new ClientTeamUpdated($payload));

        return $this;
    }

    public function addCapeAndBayAdminsToTeam(string $team_id)
    {
        $team = Team::find($team_id);
        $users = Team::whereName('Cape & Bay Admin Team')->first()->users;
        if (count($users) > 0) {
            $payload = [];
            foreach ($users as $user) {
                $payload[] = $user->id;
                UserAggregate::retrieve($user->id)
                    ->addUserToTeam($team_id, $team->name, $this->uuid())
                    ->persist();
            }

            $this->recordThat(new CapeAndBayUsersAssociatedWithClientsNewDefaultTeam($this->uuid(), $team_id, $payload));
        } else {
            throw ClientAccountException::noCapeAndBayUsersAssigned();
        }

        return $this;
    }

    public function addUserToTeam(int $user_id, string $team_id, $role)
    {
        $this->recordThat(new UserRoleOnTeamUpdated($this->uuid(), $user_id, $team_id, ['role' => $role]));

        return $this;
    }

    public function removeUserFromTeam(int $user_id, string $team_id)
    {
        $this->recordThat(new UserRemovedFromTeam($this->uuid(), $user_id, $team_id, []));

        return $this;
    }

    public function updateUserRoleOnTeam(int $user_id, string $team_id, $old_role, $role)
    {
        $this->recordThat(new UserRoleOnTeamUpdated($this->uuid(), $user_id, $team_id, ['role' => $role, 'old_role' => $old_role]));

        return $this;
    }
}
