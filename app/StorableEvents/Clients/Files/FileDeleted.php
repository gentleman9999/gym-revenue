<?php

namespace App\StorableEvents\Clients\Files;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FileDeleted extends ShouldBeStored
{
    public $user;
    public $key;
    public function __construct(string $user, string $key)
    {
        $this->user = $user;
        $this->key = $key;
    }
}
