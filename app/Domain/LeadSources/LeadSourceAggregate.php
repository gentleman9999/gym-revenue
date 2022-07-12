<?php

namespace App\Domain\LeadSources;

use App\Domain\LeadSources\Events\LeadSourceCreated;
use App\Domain\LeadSources\Events\LeadSourceUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class LeadSourceAggregate extends AggregateRoot
{
    public function create(array $payload)
    {
        $this->recordThat(new LeadSourceCreated($payload));

        return $this;
    }

    public function update(array $payload)
    {
        $this->recordThat(new LeadSourceUpdated($payload));

        return $this;
    }
}
