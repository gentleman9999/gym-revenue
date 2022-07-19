<?php

namespace App\Domain\Folders;

use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class FolderAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new FolderCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new FolderTrashed());

        return $this;
    }
}
