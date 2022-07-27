<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Events\EndUserProfilePictureMoved;
use App\Domain\EndUsers\Leads\Projections\Lead;

class LeadProfilePictureMoved extends EndUserProfilePictureMoved
{
    public function getEntity(): string
    {
        return Lead::class;
    }
}
