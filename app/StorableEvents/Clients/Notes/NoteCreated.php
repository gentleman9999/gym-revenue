<?php

namespace App\StorableEvents\Clients\Notes;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class NoteCreated extends ShouldBeStored
{
    public $client;
    public $user;
    public $payload;

    public function __construct(string $client, string $user, array $payload)
    {
        $this->client = $client;
        $this->user = $user;
        $this->payload = $payload;
    }
}
