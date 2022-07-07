<?php

namespace App\Domain\Audiences\Events;

use App\Domain\Audiences\Audience;
use App\StorableEvents\EntityRestored;

class AudienceRestored extends EntityRestored
{
    protected function getEntity(): string
    {
        return Audience::class;
    }
}
