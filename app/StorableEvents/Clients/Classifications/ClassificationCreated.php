<?php

namespace App\StorableEvents\Clients\Classifications;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ClassificationCreated extends ShouldBeStored
{
    public $client, $user, $payload;

    public function __construct(string $client, string $user, array $payload)
    {
        $this->client = $client;
        $this->user = $user;
        $this->payload = $payload;
    }
}
