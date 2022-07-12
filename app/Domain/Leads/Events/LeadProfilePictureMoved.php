<?php

namespace App\Domain\Leads\Events;

use App\Domain\Leads\Models\Lead;
use App\StorableEvents\GymRevCrudEvent;

class LeadProfilePictureMoved extends GymRevCrudEvent
{
    public array $file;
    public $oldFile;

    public function __construct(array $file, array $oldFile = null)
    {
        $this->file = $file;
        $this->oldFile = $oldFile;
    }

    protected function getEntity(): string
    {
        return Lead::class;
    }

    protected function getOperation(): string
    {
        return "PROFILE_PICTURE_MOVED";
    }
}
