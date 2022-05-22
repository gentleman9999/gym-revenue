<?php

namespace App\StorableEvents\Endusers\Leads;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SubscribedToAudience extends ShouldBeStored
{
    public $user;
    public $audience;
    public $client;
    public $entity;

    public function __construct($user, $audience, $client, $entity)
    {
        $this->user = $user;
        $this->audience = $audience;
        $this->client = $client;
        $this->entity = $entity;
    }
}
