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
            'client_id' => $event->client,
            'user_id' => $event->user,
        ];
    }

    public function addUserToTeam(User | string | null $user, $client_id = null)
    {
        $user_id = $user->id ?? $user ?? null;
        if (array_key_exists($user_id, $this->teams)) {
            // @todo - make an exception to throw here that the user is already a member
        }

        $this->recordThat(new UserAddedToTeam($this->uuid(), $user_id, $client_id));

        return $this;
    }

    public function create(array $payload)
    {
        $this->recordThat(new TeamCreated($payload));

        return $this;
    }

    public function delete()
    {
        $this->recordThat(new TeamDeleted());

        return $this;
    }

    public function update(array $payload)
    {
        $updated_by_user_id = $updated_by_user->id ?? $updated_by_user ?? null;
        $this->recordThat(new TeamUpdated($payload, $updated_by_user_id));

        return $this;
    }
}
