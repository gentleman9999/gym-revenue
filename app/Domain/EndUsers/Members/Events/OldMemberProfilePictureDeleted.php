<?php

namespace App\Domain\EndUsers\Members\Events;

use App\Domain\EndUsers\Events\OldEndUserProfilePictureDeleted;
use App\Domain\EndUsers\Members\Projections\Member;

class OldMemberProfilePictureDeleted extends OldEndUserProfilePictureDeleted
{
    public function getEntity(): string
    {
        return Member::class;
    }
}
