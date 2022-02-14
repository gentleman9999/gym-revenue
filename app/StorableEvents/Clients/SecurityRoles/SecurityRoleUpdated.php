<?php

namespace App\StorableEvents\Clients\SecurityRoles;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SecurityRoleUpdated extends ShouldBeStored
{
    public $client, $user, $payload;

    public function __construct(string $client, string $user, array $payload)
    {
        $this->client = $client;
        $this->user = $user;
        $this->payload = $payload;
    }
}
