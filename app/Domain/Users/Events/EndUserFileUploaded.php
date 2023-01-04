<?php

namespace App\Domain\Users\Events;

use App\Domain\EndUsers\Projections\EndUser;
use App\StorableEvents\EntityUpdated;

class EndUserFileUploaded extends EntityUpdated
{
    public function getEntity(): string
    {
        return EndUser::class;
    }
}
