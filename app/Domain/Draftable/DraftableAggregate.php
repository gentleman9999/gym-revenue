<?php

declare(strict_types=1);

namespace App\Domain\Draftable;

use App\Domain\Draftable\Events\DraftableCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class DraftableAggregate extends AggregateRoot
{
    public function create(array $data): static
    {
        $this->recordThat(new DraftableCreated($data));

        return $this;
    }
}
