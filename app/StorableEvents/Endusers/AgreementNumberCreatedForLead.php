<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class AgreementNumberCreatedForLead extends ShouldBeStored
{
    public $id, $client, $agreement;

    public function __construct(string $id, string $client, string $agreement)
    {
        $this->id = $id;
        $this->client = $client;
        $this->agreement = $agreement;
    }
}
