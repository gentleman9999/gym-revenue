<?php

namespace App\Domain\EndUsers\Members\Events;

use App\Domain\EndUsers\Events\EndUserDeleted;
use App\Domain\EndUsers\Members\Projections\Member;

class MemberDeleted extends EndUserDeleted
{
    public function getEntity(): string
    {
        return Member::class;
    }
}
