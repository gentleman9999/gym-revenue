<?php

namespace App\StorableEvents\Clients\Files;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FileFolderUpdated extends ShouldBeStored
{
    public $user;
    public $data;

    public function __construct(string $user, array $data)
    {
        $this->user = $user;
        $this->data = $data;
    }
}
