<?php

declare(strict_types=1);

namespace App\Domain\LeadStatuses;

use App\Domain\LeadStatuses\Events\LeadStatusCreated;
use App\Domain\LeadStatuses\Events\LeadStatusUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class LeadStatusAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new LeadStatusCreated($payload));

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new LeadStatusUpdated($payload));

        return $this;
    }
}
