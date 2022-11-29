<?php

namespace App\StorableEvents\Clients\Folders;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FolderRestored extends ShouldBeStored
{
    public $user;
    public $id;

    public function __construct(string $user, $id)
    {
        $this->user = $user;
        $this->id = $id;
    }
}
