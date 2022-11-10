<?php

declare(strict_types=1);

namespace App\Domain\BillingSchedules;

use App\Domain\BillingSchedules\Events\BillingScheduleCreated;
use App\Domain\BillingSchedules\Events\BillingScheduleDeleted;
use App\Domain\BillingSchedules\Events\BillingScheduleRestored;
use App\Domain\BillingSchedules\Events\BillingScheduleTrashed;
use App\Domain\BillingSchedules\Events\BillingScheduleUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class BillingScheduleAggregate extends AggregateRoot
{
    public function applyBillingScheduleCreated(BillingScheduleCreated $event): void
    {
        $this->billing_schedule = $event->payload;
    }

    public function create(array $payload): static
    {
        $this->recordThat(new BillingScheduleCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new BillingScheduleTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new BillingScheduleRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new BillingScheduleDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new BillingScheduleUpdated($payload));

        return $this;
    }
}
