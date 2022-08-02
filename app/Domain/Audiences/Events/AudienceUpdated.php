<?php

namespace App\Domain\Audiences\Events;

use App\Domain\Audiences\Audience;
use App\StorableEvents\EntityUpdated;

class AudienceUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return Audience::class;
    }
}
