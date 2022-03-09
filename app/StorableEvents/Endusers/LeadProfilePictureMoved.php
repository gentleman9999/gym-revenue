<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class LeadProfilePictureMoved extends ShouldBeStored
{
    public $file, $oldFile;

    public function __construct( array $file, array $oldFile = null)
    {
        $this->file = $file;
        $this->oldFile = $oldFile;
    }
}
