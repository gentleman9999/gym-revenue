<?php

namespace App\Domain\Agreements;

use App\Domain\Agreements\Events\AgreementCreated;
use App\Domain\Agreements\Events\AgreementDeleted;
use App\Domain\Agreements\Events\AgreementRestored;
use App\Domain\Agreements\Events\AgreementTrashed;
use App\Domain\Agreements\Events\AgreementUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class AgreementAggregate extends AggregateRoot
{
    public function applyAgreementCreated(AgreementCreated $event): void
    {
        $this->agreement = $event->payload;
    }

    public function create(array $payload): static
    {
        $this->recordThat(new AgreementCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new AgreementTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new AgreementRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new AgreementDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new AgreementUpdated($payload));

        return $this;
    }
}
