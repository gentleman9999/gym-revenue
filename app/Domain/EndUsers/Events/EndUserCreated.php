<?php

namespace App\Domain\EndUsers\Events;

use App\Domain\EndUsers\Projections\EndUser;
use App\StorableEvents\EntityCreated;

class EndUserCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return EndUser::class;
    }
}
