<?php

namespace App\Domain\EndUsers\Events;

use App\Domain\EndUsers\Projections\EndUser;
use App\StorableEvents\EntityRestored;

class EndUserRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return EndUser::class;
    }
}
