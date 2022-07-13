<?php

namespace App\Domain\LeadTypes;

use App\Domain\LeadTypes\Events\LeadTypeCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class LeadTypeAggregate extends AggregateRoot
{
    public function create(array $payload)
    {
        $this->recordThat(new LeadTypeCreated($payload));

        return $this;
    }
//
//    public function update(array $payload)
//    {
//        $this->recordThat(new LeadTypeUpdated($payload));
//
//        return $this;
//    }
}
