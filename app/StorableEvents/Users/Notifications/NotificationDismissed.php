<?php

namespace App\StorableEvents\Users\Notifications;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class NotificationDismissed extends ShouldBeStored
{
    public $user;
    public $id;

    public function __construct(string $user, string $id)
    {
        $this->user = $user;
        $this->id = $id;
    }
}
