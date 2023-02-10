<?php

declare(strict_types=1);

namespace App\StorableEvents\Clients\Notes;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class NoteTrashed extends ShouldBeStored
{
    public $client;
    public $user;
    public $id;

    public function __construct(string $client, string $user, string $id)
    {
        $this->client = $client;
        $this->user   = $user;
        $this->id     = $id;
    }
}
