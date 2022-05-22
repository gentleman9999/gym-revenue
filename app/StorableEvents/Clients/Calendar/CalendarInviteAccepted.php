<?php

namespace App\StorableEvents\Clients\Calendar;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class CalendarInviteAccepted extends ShouldBeStored
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
