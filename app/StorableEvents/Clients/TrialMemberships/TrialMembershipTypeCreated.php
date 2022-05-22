<?php

namespace App\StorableEvents\Clients\TrialMemberships;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TrialMembershipTypeCreated extends ShouldBeStored
{
    public $client;
    public $data;
    public $user;

    public function __construct(string $client, array $data, int $user)
    {
        $this->client = $client;
        $this->data = $data;
        $this->user = $user;
    }
}
