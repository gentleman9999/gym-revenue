<?php

declare(strict_types=1);

namespace App\Domain\EntrySourceCategories;

use App\Domain\EntrySourceCategories\Events\EntrySourceCategoryCreated;
use App\Domain\EntrySourceCategories\Events\EntrySourceCategoryDeleted;
use App\Domain\EntrySourceCategories\Events\EntrySourceCategoryRestored;
use App\Domain\EntrySourceCategories\Events\EntrySourceCategoryTrashed;
use App\Domain\EntrySourceCategories\Events\EntrySourceCategoryUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class EntrySourceCategoryAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new EntrySourceCategoryCreated($payload));

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new EntrySourceCategoryUpdated($payload));

        return $this;
    }

    public function trash(array $payload): static
    {
        $this->recordThat(new EntrySourceCategoryTrashed($payload));

        return $this;
    }

    public function restore(array $payload): static
    {
        $this->recordThat(new EntrySourceCategoryRestored($payload));

        return $this;
    }

    public function delete(array $payload): static
    {
        $this->recordThat(new EntrySourceCategoryDeleted($payload));

        return $this;
    }
}
