<?php

namespace App\Domain\EndUsers\Members\Events;

use App\Domain\EndUsers\Events\EndUserWasCalledByRep;
use App\Domain\EndUsers\Members\Projections\Member;

class MemberWasCalledByRep extends EndUserWasCalledByRep
{
    public function getEntity(): string
    {
        return Member::class;
    }
}
