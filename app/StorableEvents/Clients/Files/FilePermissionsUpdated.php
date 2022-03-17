<?php

namespace App\StorableEvents\Clients\Files;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FilePermissionsUpdated extends ShouldBeStored
{
    public $user, $data;

    public function __construct(string $user, array $data)
    {
        $this->user = $user;
        $this->data = $data;
    }
}
