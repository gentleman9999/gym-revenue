<?php

declare(strict_types=1);

namespace App\Domain\MemberGroups;

use App\Domain\MemberGroups\Events\MemberGroupCreated;
use App\Domain\MemberGroups\Events\MemberGroupDeleted;
use App\Domain\MemberGroups\Events\MemberGroupRestored;
use App\Domain\MemberGroups\Events\MemberGroupTrashed;
use App\Domain\MemberGroups\Events\MemberGroupUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class MemberGroupAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new MemberGroupCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new MemberGroupTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new MemberGroupRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new MemberGroupDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new MemberGroupUpdated($payload));

        return $this;
    }
}
