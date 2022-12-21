<?php

declare(strict_types=1);

namespace App\Domain\UserMemberGroups;

use App\Domain\UserMemberGroups\Events\UserMemberGroupCreated;
use App\Domain\UserMemberGroups\Events\UserMemberGroupDeleted;
use App\Domain\UserMemberGroups\Events\UserMemberGroupUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class UserMemberGroupAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new UserMemberGroupCreated($payload));

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new UserMemberGroupDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new UserMemberGroupUpdated($payload));

        return $this;
    }
}
