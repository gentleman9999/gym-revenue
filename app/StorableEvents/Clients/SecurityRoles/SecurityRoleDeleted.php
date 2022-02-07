<?php

namespace App\StorableEvents\Clients\SecurityRoles;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SecurityRoleDeleted extends ShouldBeStored
{
    public $client, $user, $id;

    public function __construct(string $client, string $user, string $id)
    {
        $this->client = $client;
        $this->user = $user;
        $this->id = $id;
    }
}
