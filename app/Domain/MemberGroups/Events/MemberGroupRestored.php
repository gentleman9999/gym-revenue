<?php

declare(strict_types=1);

namespace App\Domain\MemberGroups\Events;

use App\Domain\MemberGroups\Projections\MemberGroup;
use App\StorableEvents\EntityRestored;

class MemberGroupRestored extends EntityRestored
{
    public function getEntity(): string
    {
        return MemberGroup::class;
    }
}
