<?php

namespace App\StorableEvents\Clients\Files;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FileDeleted extends ShouldBeStored
{
    public $user;
    public $id;

    public function __construct(string $user, string $id)
    {
        $this->user = $user;
        $this->id = $id;
    }
}
