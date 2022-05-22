<?php

namespace App\StorableEvents\Clients\Roles;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class RoleCreated extends ShouldBeStored
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
