<?php

namespace App\StorableEvents\Clients\Tasks;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TaskTrashed extends ShouldBeStored
{
    public $user, $id, $client;
    public function __construct(string $client, string $user, $id)
    {
        $this->client = $client;
        $this->user = $user;
        $this->id = $id;
    }
}
