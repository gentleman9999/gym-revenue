<?php

namespace App\StorableEvents\Clients\Classifications;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ClassificationRestored extends ShouldBeStored
{
    public $client, $user, $id;

    public function __construct(string $client, string $user, string $id)
    {
        $this->client = $client;
        $this->user = $user;
        $this->id = $id;
    }
}
