<?php

declare(strict_types=1);

namespace App\Domain\EntrySources\Events;

use App\Domain\EntrySources\EntrySource;
use App\StorableEvents\EntityCreated;

class EntrySourceCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return EntrySource::class;
    }
}
