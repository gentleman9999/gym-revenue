<?php

namespace App\Domain\EndUsers\Members\Events;

use App\Domain\EndUsers\Events\EndUserUpdated;
use App\Domain\EndUsers\Members\Projections\Member;

class MemberUpdated extends EndUserUpdated
{
    public function getEntity(): string
    {
        return Member::class;
    }
}
