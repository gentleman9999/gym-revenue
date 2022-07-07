<?php

namespace App\Domain\Audiences\Events;

use App\Domain\Audiences\Audience;
use App\StorableEvents\EntityDeleted;

class AudienceDeleted extends EntityDeleted
{
    protected function getEntity(): string
    {
        return Audience::class;
    }
}
