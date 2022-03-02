<?php

namespace App\StorableEvents\Clients\Files;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FileTrashed extends ShouldBeStored
{
    public $user, $id;
    public function __construct(string $user, $id)
    {
        $this->user = $user;
        $this->id = $id;
    }
}
