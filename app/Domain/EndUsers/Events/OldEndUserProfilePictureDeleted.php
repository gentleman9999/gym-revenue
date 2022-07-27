<?php

namespace App\Domain\EndUsers\Events;

use App\StorableEvents\GymRevCrudEvent;

abstract class OldEndUserProfilePictureDeleted extends GymRevCrudEvent
{
    public array $file;

    public function __construct(array $file)
    {
        $this->file = $file;
    }

    protected function getOperation(): string
    {
        return "OLD_PROFILE_PICTURE_DELETED";
    }
}
