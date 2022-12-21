<?php

declare(strict_types=1);

namespace App\Domain\UserMemberGroups\Events;

use App\Domain\UserMemberGroups\Projections\UserMemberGroup;
use App\StorableEvents\EntityUpdated;

class UserMemberGroupUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return UserMemberGroup::class;
    }
}
