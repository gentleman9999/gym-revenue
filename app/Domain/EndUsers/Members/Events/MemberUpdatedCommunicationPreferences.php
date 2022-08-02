<?php

namespace App\Domain\EndUsers\Members\Events;

use App\Domain\EndUsers\Events\EndUserUpdatedCommunicationPreferences;
use App\Domain\EndUsers\Members\Projections\Member;

class MemberUpdatedCommunicationPreferences extends EndUserUpdatedCommunicationPreferences
{
    public function getEntity(): string
    {
        return Member::class;
    }
}
