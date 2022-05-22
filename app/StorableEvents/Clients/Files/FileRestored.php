<?php

namespace App\StorableEvents\Clients\Files;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FileRestored extends ShouldBeStored
{
    public $user;
    public $id;

    public function __construct(string $user, $id)
    {
        $this->user = $user;
        $this->id = $id;
    }
}
