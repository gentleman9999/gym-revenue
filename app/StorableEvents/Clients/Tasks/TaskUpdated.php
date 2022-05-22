<?php

namespace App\StorableEvents\Clients\Tasks;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TaskUpdated extends ShouldBeStored
{
    public $user;
    public $data;
    public $client;

    public function __construct(string $client, string $user, array $data)
    {
        $this->client = $client;
        $this->user = $user;
        $this->data = $data;
    }
}
