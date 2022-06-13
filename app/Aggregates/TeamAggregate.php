<?php

namespace App\Aggregates;

use App\StorableEvents\Teams\TeamCreated;
use App\StorableEvents\Teams\TeamDeleted;
use App\StorableEvents\Teams\TeamUpdated;
use App\StorableEvents\Users\UserAddedToTeam;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class TeamAggregate extends AggregateRoot
{
    public function applyUserAddedToTeam(UserAddedToTeam $event)
    {
        $this->teams[$event->team] = [
            'team_id' => $event->team,
            'team_name' => $event->name,
            'client_id' => $event->client,
        ];
    }

    public function addUserToTeam(string $team_id, string $team_name, $client_id = null)
    {
        if (array_key_exists($team_id, $this->teams)) {
            // @todo - make an exception to throw here that the user is already a member
        }

        $this->recordThat(new UserAddedToTeam($this->uuid(), $team_id, $team_name, $client_id));

        return $this;
    }

    public function createTeam(array $payload, string $created_by_user_id = 'Auto Generated')
    {
        $this->recordThat(new TeamCreated($this->uuid(), $created_by_user_id, $payload));

        return $this;
    }

    public function delete(string  $id, string $deleted_by_user_id)
    {
        $this->recordThat(new TeamDeleted($this->uuid(), $deleted_by_user_id, $id));

        return $this;
    }

    public function update(array $payload, string $updated_by_user_id)
    {
        $this->recordThat(new TeamUpdated($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }
}
