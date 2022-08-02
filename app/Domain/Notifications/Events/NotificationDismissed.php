<?php

namespace App\Domain\Notifications\Events;

use App\Domain\Notifications\Notification;
use App\StorableEvents\EntityTrashed;

class NotificationDismissed extends EntityTrashed
{
    public function getEntity(): string
    {
        return Notification::class;
    }
}
