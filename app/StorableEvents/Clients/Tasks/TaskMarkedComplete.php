<?php

namespace App\StorableEvents\Clients\Tasks;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TaskMarkedComplete extends ShouldBeStored
{
    public $user;
    public $id;
    public $client;

    public function __construct(string $client, string $user, string $id)
    {
        $this->client = $client;
        $this->user = $user;
        $this->id = $id;
    }
}
