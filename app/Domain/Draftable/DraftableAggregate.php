<?php

declare(strict_types=1);

namespace App\Domain\Draftable;

use App\Domain\Draftable\Events\DraftableCreated;
use App\Domain\Draftable\Events\DraftableDeleted;
use App\Domain\Draftable\Events\DraftableUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class DraftableAggregate extends AggregateRoot
{
    public function create(array $data): static
    {
        $this->recordThat(new DraftableCreated($data));

        return $this;
    }

    public function update(array $data): static
    {
        $this->recordThat(new DraftableUpdated($data));

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new DraftableDeleted());

        return $this;
    }
}
