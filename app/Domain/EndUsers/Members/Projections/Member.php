<?php

namespace App\Domain\EndUsers\Members\Projections;

use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Projections\EndUserDetails;

class Member extends EndUser
{
    public static function getDetailsModel(): EndUserDetails
    {
        return new MemberDetails();
    }
}
