<?php

declare(strict_types=1);

namespace App\Domain\Audiences\Events;

use App\Domain\Audiences\Audience;
use App\StorableEvents\EntityTrashed;

class AudienceTrashed extends EntityTrashed
{
    public function getEntity(): string
    {
        return Audience::class;
    }
}
