<?php

namespace App\Domain\EndUsers\Leads\Events;

use App\Domain\EndUsers\Events\OldEndUserProfilePictureDeleted;
use App\Domain\EndUsers\Leads\Projections\Lead;

class OldLeadProfilePictureDeleted extends OldEndUserProfilePictureDeleted
{
    public function getEntity(): string
    {
        return Lead::class;
    }
}
