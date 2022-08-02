<?php

namespace App\Domain\EndUsers\Members\Events;

use App\Domain\EndUsers\Events\EndUserCreated;
use App\Domain\EndUsers\Members\Projections\Member;

class MemberCreated extends EndUserCreated
{
    public function getEntity(): string
    {
        return Member::class;
    }
}
