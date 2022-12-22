<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments;

use App\Domain\StructuredDocuments\Events\StructuredDocumentCreated;
use App\Domain\StructuredDocuments\Events\StructuredDocumentDeleted;
use App\Domain\StructuredDocuments\Events\StructuredDocumentRestored;
use App\Domain\StructuredDocuments\Events\StructuredDocumentTrashed;
use App\Domain\StructuredDocuments\Events\StructuredDocumentUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class StructuredDocumentAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new StructuredDocumentCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new StructuredDocumentTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new StructuredDocumentRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new StructuredDocumentDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new StructuredDocumentUpdated($payload));

        return $this;
    }
}
