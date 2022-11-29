<?php

namespace App\Domain\EndUsers\Events;

use App\StorableEvents\GymRevCrudEvent;

class EndUserProfilePictureMoved extends GymRevCrudEvent
{
    public array $file;
    public ?array $oldFile;

    public function __construct(array $file, array $oldFile = null)
    {
        parent::__construct();
        $this->file = $file;
        $this->oldFile = $oldFile;
    }

    protected function getOperation(): string
    {
        return "PROFILE_PICTURE_MOVED";
    }

    public function getEntity(): string
    {
        return EndUser::class;
    }
}
