<?php

namespace App\Domain\Teams;

use App\Aggregates\User;
use App\Domain\Teams\Events\TeamCreated;
use App\Domain\Teams\Events\TeamDeleted;
use App\Domain\Teams\Events\TeamMemberAdded;
use App\Domain\Teams\Events\TeamMemberRemoved;
use App\Domain\Teams\Events\TeamRestored;
use App\Domain\Teams\Events\TeamTrashed;
use App\Domain\Teams\Events\TeamUpdated;
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

    public function addUserToTeam(User | string | null $user, $client_id = null): static
    {
        $user_id = $user->id ?? $user ?? null;
        if (array_key_exists($user_id, $this->teams)) {
            // @todo - make an exception to throw here that the user is already a member
        }

        $this->recordThat(new UserAddedToTeam($this->uuid(), $user_id, $client_id));

        return $this;
    }

    public function create(array $payload): static
    {
        $this->recordThat(new TeamCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new TeamTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new TeamRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new TeamDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new TeamUpdated($payload));

        return $this;
    }

    public function inviteMember(string $email): static
    {
        $this->recordThat(new TeamMemberInvited($email));

        return $this;
    }

    public function addMember(string $email): static
    {
        $this->recordThat(new TeamMemberAdded($email));

        return $this;
    }

    public function removeMember(string $email): static
    {
        $this->recordThat(new TeamMemberRemoved($email));

        return $this;
    }
}
