<?php

namespace App\StorableEvents\Clients\ClientServices;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class TrialMembershipTypeUpdated extends ShouldBeStored
{
    public $client, $data, $user;

    public function __construct(string $client, array $data, int $user)
    {
        $this->client = $client;
        $this->data = $data;
        $this->user = $user;
    }
}
