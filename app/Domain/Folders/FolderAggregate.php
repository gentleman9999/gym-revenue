<?php

namespace App\Domain\Folders;

use App\Domain\Folders\Events\FolderCreated;
use App\Domain\Folders\Events\FolderDeleted;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class FolderAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new FolderCreated($payload));

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new FolderDeleted());

        return $this;
    }
}
