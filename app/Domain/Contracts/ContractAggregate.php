<?php

declare(strict_types=1);

namespace App\Domain\Contracts;

use App\Domain\Contracts\Events\ContractCreated;
use App\Domain\Contracts\Events\ContractDeleted;
use App\Domain\Contracts\Events\ContractRestored;
use App\Domain\Contracts\Events\ContractTrashed;
use App\Domain\Contracts\Events\ContractUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ContractAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new ContractCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new ContractTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new ContractRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new ContractDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new ContractUpdated($payload));

        return $this;
    }
}
