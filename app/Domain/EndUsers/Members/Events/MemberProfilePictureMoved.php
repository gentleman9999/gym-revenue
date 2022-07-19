<?php

namespace App\Domain\EndUsers\Members\Events;

use App\Domain\EndUsers\Events\EndUserProfilePictureMoved;
use App\Domain\EndUsers\Members\Projections\Member;

class MemberProfilePictureMoved extends EndUserProfilePictureMoved
{
    public function getEntity(): string
    {
        return Member::class;
    }
}
