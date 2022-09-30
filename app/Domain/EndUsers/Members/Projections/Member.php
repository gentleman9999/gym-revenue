<?php

namespace App\Domain\EndUsers\Members\Projections;

use App\Domain\Clients\Projections\Client;
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

    public function getPhoneNumber(): ?string
    {
        return $this->primary_phone;
    }
}
