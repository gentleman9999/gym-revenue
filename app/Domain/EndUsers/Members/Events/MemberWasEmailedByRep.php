<?php

namespace App\Domain\EndUsers\Members\Events;

use App\Domain\EndUsers\Events\EndUserWasEmailedByRep;
use App\Domain\EndUsers\Members\Projections\Member;

class MemberWasEmailedByRep extends EndUserWasEmailedByRep
{
    public function getEntity(): string
    {
        return Member::class;
    }
}
