<?php

declare(strict_types=1);

namespace App\Domain\DynamicReports;

use App\Domain\DynamicReports\Events\DynamicReportCreated;
use App\Domain\DynamicReports\Events\DynamicReportDeleted;
use App\Domain\DynamicReports\Events\DynamicReportRestored;
use App\Domain\DynamicReports\Events\DynamicReportTrashed;
use App\Domain\DynamicReports\Events\DynamicReportUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class DynamicReportAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new DynamicReportCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new DynamicReportTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new DynamicReportRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new DynamicReportDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new DynamicReportUpdated($payload));

        return $this;
    }
}
