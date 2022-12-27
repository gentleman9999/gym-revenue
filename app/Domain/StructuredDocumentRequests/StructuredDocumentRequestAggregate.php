<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocumentRequests;

use App\Domain\StructuredDocumentRequests\Events\StructuredDocumentRequestCreated;
use App\Domain\StructuredDocumentRequests\Events\StructuredDocumentRequestDeleted;
use App\Domain\StructuredDocumentRequests\Events\StructuredDocumentRequestRestored;
use App\Domain\StructuredDocumentRequests\Events\StructuredDocumentRequestTrashed;
use App\Domain\StructuredDocumentRequests\Events\StructuredDocumentRequestUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class StructuredDocumentRequestAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new StructuredDocumentRequestCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new StructuredDocumentRequestTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new StructuredDocumentRequestRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new StructuredDocumentRequestDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new StructuredDocumentRequestUpdated($payload));

        return $this;
    }
}
