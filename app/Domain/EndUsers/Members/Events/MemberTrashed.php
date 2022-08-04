<?php

namespace App\Domain\EndUsers\Members\Events;

use App\Domain\EndUsers\Events\EndUserTrashed;
use App\Domain\EndUsers\Members\Projections\Member;

class MemberTrashed extends EndUserTrashed
{
    public function getEntity(): string
    {
        return Member::class;
    }
}
