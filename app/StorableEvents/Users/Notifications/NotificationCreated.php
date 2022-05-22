<?php

namespace App\StorableEvents\Users\Notifications;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class NotificationCreated extends ShouldBeStored
{
    public $user;
    public $data;

    public function __construct(string $user, array $data)
    {
        $this->user = $user;
        $this->data = $data;
    }
}
