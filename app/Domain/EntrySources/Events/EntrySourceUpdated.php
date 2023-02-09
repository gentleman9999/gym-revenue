<?php

declare(strict_types=1);

namespace App\Domain\EntrySources\Events;

use App\Domain\EntrySources\EntrySource;
use App\StorableEvents\EntityUpdated;

class EntrySourceUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return EntrySource::class;
    }
}
