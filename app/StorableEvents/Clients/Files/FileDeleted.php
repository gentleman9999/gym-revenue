<?php

declare(strict_types=1);

namespace App\StorableEvents\Clients\Files;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class FileDeleted extends ShouldBeStored
{
    public $user;
    public $id;
    public $data;

    public function __construct(string $user, string $id, $data)
    {
        $this->user = $user;
        $this->id   = $id;
        $this->data = $data;
    }
}
