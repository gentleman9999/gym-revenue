<?php

namespace App\Domain\EndUsers\Members\Reactors;

use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Members\MemberAggregate;
use App\Domain\EndUsers\Members\Projections\Member;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Reactors\EndUserActivityReactor;

class MemberActivityActivityReactor extends EndUserActivityReactor
{
    public static function getModel(): EndUser
    {
        return new Member();
    }

    public static function getAggregate(): EndUserAggregate
    {
        return new MemberAggregate();
    }
}
