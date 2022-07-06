<?php

namespace App\Domain\Audiences\Events;

use App\Domain\Audiences\Audience;
use App\StorableEvents\EntityTrashed;

class AudienceTrashed extends EntityTrashed
{
    protected function getEntity(): string
    {
        return Audience::class;
    }
}
