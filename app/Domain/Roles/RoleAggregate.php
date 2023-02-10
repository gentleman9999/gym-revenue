<?php

declare(strict_types=1);

namespace App\Domain\Roles;

use App\Domain\Roles\Events\RoleCreated;
use App\Domain\Roles\Events\RoleDeleted;
use App\Domain\Roles\Events\RoleUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class RoleAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new RoleCreated($payload));

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new RoleDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new RoleUpdated($payload));

        return $this;
    }
}
