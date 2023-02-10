<?php

declare(strict_types=1);

namespace App\Aggregates\Clients;

use App\StorableEvents\Clients\Folders\FolderRestored;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class FolderAggregate extends AggregateRoot
{
    public function restore(string $userId)
    {
        $this->recordThat(new FolderRestored($userId, $this->uuid()));

        return $this;
    }
}
