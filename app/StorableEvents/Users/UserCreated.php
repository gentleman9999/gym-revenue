<?php

namespace App\StorableEvents\Users;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserCreated extends ShouldBeStored
{
    public string $id;
    public string $user;
    public array $payload;

    public function __construct(string $id, string $user, array $payload)
    {
        $this->id = $id;
        $this->user = $user;
        $this->payload = $payload;
    }
}
