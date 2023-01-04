<?php

declare(strict_types=1);

namespace App\Domain\MemberGroups\Events;

use App\Domain\MemberGroups\Projections\MemberGroup;
use App\StorableEvents\EntityDeleted;

class MemberGroupDeleted extends EntityDeleted
{
    public function getEntity(): string
    {
        return MemberGroup::class;
    }
}
