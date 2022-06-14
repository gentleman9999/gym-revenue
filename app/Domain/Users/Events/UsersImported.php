<?php

namespace App\Domain\Users\Events;

use App\StorableEvents\GymRevShouldBeStored;

class UsersImported extends GymRevShouldBeStored
{
    public $key;
    public $client;

    public function __construct(string $key, string $client)
    {
        $this->key = $key;
        $this->client = $client;
    }
}
