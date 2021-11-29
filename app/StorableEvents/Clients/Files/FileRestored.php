<?php

namespace App\StorableEvents\Clients\Files;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FileRestored extends ShouldBeStored
{
    public $user;
    public function __construct(string $user)
    {
        $this->user = $user;
    }
}
