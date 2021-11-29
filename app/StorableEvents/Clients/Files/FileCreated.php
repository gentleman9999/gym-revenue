<?php

namespace App\StorableEvents\Clients\Files;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FileCreated extends ShouldBeStored
{
    public $user, $tmpKey, $fileId;

    public function __construct(string $user, string $tmpKey, $fileId)
    {
        $this->user = $user;
        $this->tmpKey = $tmpKey;
        $this->fileId = $fileId;
    }
}
