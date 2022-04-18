<?php

namespace App\StorableEvents\Endusers\Members;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MemberCreated extends ShouldBeStored
{
    public $user, $data, $client;

    public function __construct(string $client, string $user, array $data)
    {
        $this->client = $client;
        $this->user = $user;
        $this->data = $data;
    }
}
