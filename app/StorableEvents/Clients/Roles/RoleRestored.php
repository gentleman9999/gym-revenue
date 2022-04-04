<?php

namespace App\StorableEvents\Clients\Roles;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class RoleRestored extends ShouldBeStored
{
    public $client, $user, $id;

    public function __construct(string $client, string $user, string $id)
    {
        $this->client = $client;
        $this->user = $user;
        $this->id = $id;
    }
}
