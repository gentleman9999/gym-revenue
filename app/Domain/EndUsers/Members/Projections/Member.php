<?php

namespace App\Domain\EndUsers\Members\Projections;

use App\Domain\Clients\Projections\Client;
use App\Domain\EndUsers\Members\MemberAggregate;
use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Projections\EndUserDetails;

/**
 * @property Client $client
 */
class Member extends EndUser
{
    public static function getDetailsModel(): EndUserDetails
    {
        return new MemberDetails();
    }

    public function getInteractionCount()
    {
        $aggy = MemberAggregate::retrieve($this->id);

        return $aggy->getInteractionCount();
    }
}
