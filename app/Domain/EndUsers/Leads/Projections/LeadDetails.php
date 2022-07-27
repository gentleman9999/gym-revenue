<?php

namespace App\Domain\EndUsers\Leads\Projections;

use App\Domain\EndUsers\Projections\EndUser;
use App\Domain\EndUsers\Projections\EndUserDetails;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadDetails extends EndUserDetails
{
    public function lead(): BelongsTo
    {
        return $this->endUser();
    }

    public static function getRelatedModel(): EndUser
    {
        return new Lead();
    }

    public static function fk(): string
    {
        return "lead_id";
    }
}
