<?php

declare(strict_types=1);

namespace App\Domain\MemberGroups\Events;

use App\Domain\MemberGroups\Projections\MemberGroup;
use App\StorableEvents\EntityUpdated;

class MemberGroupUpdated extends EntityUpdated
{
    public function getEntity(): string
    {
        return MemberGroup::class;
    }
}
