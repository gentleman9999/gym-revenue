<?php

declare(strict_types=1);

namespace App\Domain\UserMemberGroups\Events;

use App\Domain\UserMemberGroups\Projections\UserMemberGroup;
use App\StorableEvents\EntityCreated;

class UserMemberGroupCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return UserMemberGroup::class;
    }
}
