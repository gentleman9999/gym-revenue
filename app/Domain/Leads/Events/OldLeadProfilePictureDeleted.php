<?php

namespace App\Domain\Leads\Events;

use App\Domain\Leads\Models\Lead;
use App\StorableEvents\GymRevCrudEvent;

class OldLeadProfilePictureDeleted extends GymRevCrudEvent
{
    public array $file;

    public function __construct(array $file)
    {
        $this->file = $file;
    }

    protected function getEntity(): string
    {
        return Lead::class;
    }

    protected function getOperation(): string
    {
        return "OLD_PROFILE_PICTURE_DELETED";
    }
}
