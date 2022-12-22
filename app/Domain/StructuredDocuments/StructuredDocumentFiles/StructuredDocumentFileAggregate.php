<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\StructuredDocumentFiles;

use App\Domain\StructuredDocuments\StructuredDocumentFiles\Events\StructuredDocumentFileCreated;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Events\StructuredDocumentFileDeleted;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Events\StructuredDocumentFileRestored;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Events\StructuredDocumentFileTrashed;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Events\StructuredDocumentFileUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class StructuredDocumentFileAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new StructuredDocumentFileCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new StructuredDocumentFileTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new StructuredDocumentFileRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new StructuredDocumentFileDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new StructuredDocumentFileUpdated($payload));

        return $this;
    }
}
