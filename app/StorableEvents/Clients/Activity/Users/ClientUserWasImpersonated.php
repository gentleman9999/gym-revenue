<?php

namespace App\StorableEvents\Clients\Activity\Users;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class ClientUserWasImpersonated extends ShouldBeStored
{
    public $client, $employee, $invader, $date;
    public function __construct($client, $employee, $invader, $date)
    {
        $this->client = $client;
        $this->employee = $employee;
        $this->invader = $invader;
        $this->date = $date;
    }
}
