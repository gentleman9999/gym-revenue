<?php

namespace App\Domain\EndUsers\Members\Events;

use App\Domain\EndUsers\Events\EndUserClaimedByRep;
use App\Domain\EndUsers\Members\Projections\Member;

class MemberClaimedByRep extends EndUserClaimedByRep
{
    public function getEntity(): string
    {
        return Member::class;
    }
}
