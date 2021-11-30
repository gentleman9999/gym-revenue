<?php

namespace App\StorableEvents\Clients\Files;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FileRenamed extends ShouldBeStored
{
    public $user, $oldFilename, $newFilename;
    public function __construct(string $user, string $oldFilename, string $newFilename)
    {
        $this->user = $user;
        $this->oldFilename = $oldFilename;
        $this->newFilename = $newFilename;
    }
}
