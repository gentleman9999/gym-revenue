<?php

namespace App\Domain\EndUsers\Members\Events;

use App\Domain\EndUsers\Events\EndUserRestored;
use App\Domain\EndUsers\Members\Projections\Member;

class MemberRestored extends EndUserRestored
{
    public function getEntity(): string
    {
        return Member::class;
    }
}
