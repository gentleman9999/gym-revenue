<?php

declare(strict_types=1);

namespace App\Domain\Folders;

use App\Domain\Folders\Events\FolderCreated;
use App\Domain\Folders\Events\FolderDeleted;
use App\Domain\Folders\Events\FolderSharingUpdated;
use App\Domain\Folders\Events\FolderTrashed;
use App\Domain\Folders\Events\FolderUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class FolderAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new FolderCreated($payload));

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new FolderUpdated($payload));

        return $this;
    }

    public function updateSharing(array $payload): static
    {
        $this->recordThat(new FolderSharingUpdated($payload));

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new FolderDeleted());

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new FolderTrashed());

        return $this;
    }
}
