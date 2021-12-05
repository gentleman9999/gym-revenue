<?php

namespace App\StorableEvents\Endusers;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SubscribedToAudience extends ShouldBeStored
{
    public $user, $audience, $client, $entity;

    public function __construct($user, $audience, $client, $entity)
    {
        $this->user = $user;
        $this->audience = $audience;
        $this->client = $client;
        $this->entity = $entity;
    }
}
