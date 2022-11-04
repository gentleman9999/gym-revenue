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

    public function isCBorGR(EndUser $user)
    {
        return (str_ends_with($user['email'], '@capeandbay.com') || str_ends_with($user['email'], '@gymrevenue.com'));
    }
}
