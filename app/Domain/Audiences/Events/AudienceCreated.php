<?php

namespace App\Domain\Audiences\Events;

use App\Domain\Audiences\Audience;
use App\StorableEvents\EntityCreated;

class AudienceCreated extends EntityCreated
{
    protected function getEntity(): string
    {
        return Audience::class;
    }
}
