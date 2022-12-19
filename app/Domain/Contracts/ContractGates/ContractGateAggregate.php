<?php

declare(strict_types=1);

namespace App\Domain\Contracts\ContractGates;

use App\Domain\Contracts\ContractGates\Events\ContractGateCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ContractGateAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new ContractGateCreated($payload));

        return $this;
    }
}
