<?php

namespace App\Domain\EndUsers\Events;

use App\Domain\EndUsers\Projections\EndUser;
use App\StorableEvents\EntityDeleted;

class EndUserDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return EndUser::class;
    }
}
