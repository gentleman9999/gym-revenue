<?php

namespace App\StorableEvents\Users;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UsersImported extends ShouldBeStored
{
    public $id;
    public $user;
    public $key;
    public $client;

    public function __construct(string $id, string $user, string $key, string $client)
    {
        $this->id = $id;
        $this->user = $user;
        $this->key = $key;
        $this->client = $client;
    }
}
