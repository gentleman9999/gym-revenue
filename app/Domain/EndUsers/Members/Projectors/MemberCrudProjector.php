<?php

namespace App\Domain\EndUsers\Members\Projectors;

use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\EndUsers\Members\Projections\MemberDetails;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Projectors\EndUserCrudProjector;

class MemberCrudProjector extends EndUserCrudProjector
{
    public function onStartingEventReplay()
    {
        Member::truncate();
        MemberDetails::truncate();
    }

    protected function getModel(): EndUser
    {
        return new Member();
    }
}
