<?php

namespace App\Domain\EndUsers\Members\Events;

use App\Domain\EndUsers\Events\EndUserWasTextMessagedByRep;
use App\Domain\EndUsers\Members\Projections\Member;

class MemberWasTextMessagedByRep extends EndUserWasTextMessagedByRep
{
    public function getEntity(): string
    {
        return Member::class;
    }
}
