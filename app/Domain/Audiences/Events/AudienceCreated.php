<?php

declare(strict_types=1);

namespace App\Domain\Audiences\Events;

use App\Domain\Audiences\Audience;
use App\StorableEvents\EntityCreated;

class AudienceCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Audience::class;
    }
}
