<?php

namespace App\Domain\EndUsers\Members\Projections;

use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Projections\EndUserDetails;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberDetails extends EndUserDetails
{
    public static function getRelatedModel(): EndUser
    {
        return new Member();
    }

    public static function fk(): string
    {
        return "member_id";
    }

    public function member(): BelongsTo
    {
        return $this->endUser();
    }
}
