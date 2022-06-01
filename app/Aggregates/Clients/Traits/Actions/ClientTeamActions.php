<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\Aggregates\Users\UserAggregate;
use App\Exceptions\Clients\ClientAccountException;
use App\Models\Team;
use App\Models\UserDetails;
use App\StorableEvents\Clients\CapeAndBayUsersAssociatedWithClientsNewDefaultTeam;
use App\StorableEvents\Clients\PrefixCreated;
use App\StorableEvents\Clients\Teams\ClientTeamCreated;
use App\StorableEvents\Clients\Teams\ClientTeamDeleted;
use App\StorableEvents\Clients\Teams\ClientTeamUpdated;
use App\StorableEvents\Clients\UserRemovedFromTeam;
use App\StorableEvents\Clients\UserRoleOnTeamUpdated;

trait ClientTeamActions
{
    public function createTeamPrefix(string $prefix)
    {
        if (! empty($this->team_prefix)) {
            throw ClientAccountException::prefixAlreadyCreated($this->team_prefix, $this->default_team);
        } else {
            $this->recordThat(new PrefixCreated($this->uuid(), $prefix));
        }

        return $this;
    }

    public function createTeam(array $payload, string $created_by_user_id = 'Auto Generated')
    {
        if (array_key_exists($payload['id'], $this->teams)) {
            throw ClientAccountException::teamAlreadyAssigned($payload['name']);
        }
        // @todo - make sure the team is not assigned to another client

        $this->recordThat(new ClientTeamCreated($this->uuid(), $created_by_user_id, $payload));

        return $this;
    }

    public function deleteTeam($id, string $deleted_by_user_id)
    {
        $this->recordThat(new ClientTeamDeleted($this->uuid(), $deleted_by_user_id, $id));

        return $this;
    }

    public function updateTeam(array $payload, string $updated_by_user_id)
    {
        $this->recordThat(new ClientTeamUpdated($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }

    public function addCapeAndBayAdminsToTeam(string $team_id)
    {
        $team = Team::find($team_id);
        $users = UserDetails::select('user_id')
            ->whereName('default_team')
            ->whereValue(1)->get();

        if (count($users) > 0) {
            $payload = [];
            foreach ($users as $user) {
                $payload[] = $user->user_id;
                UserAggregate::retrieve($user->user_id)
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
