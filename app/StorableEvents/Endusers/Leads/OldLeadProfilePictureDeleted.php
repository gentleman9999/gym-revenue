<?php

namespace App\StorableEvents\Endusers\Leads;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class OldLeadProfilePictureDeleted extends ShouldBeStored
{
    public $file;

    public function __construct( array $file)
    {
        $this->file = $file;
    }
}
