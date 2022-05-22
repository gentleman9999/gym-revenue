<?php

namespace App\StorableEvents\Endusers\Members;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class MemberTrashed extends ShouldBeStored
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
