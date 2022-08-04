<?php

namespace App\Domain\EndUsers\Members\Actions;

use App\Domain\EndUsers\Actions\ReadEndUsers;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Members\MemberAggregate;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\EndUsers\Projections\EndUser;

class ReadMembers extends ReadEndUsers
{
    protected static function getModel(): EndUser
    {
        return new Member();
    }

    protected static function getAggregate(): EndUserAggregate
    {
        return new MemberAggregate();
    }
}
