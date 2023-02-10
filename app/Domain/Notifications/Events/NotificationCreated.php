<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Events;

use App\Domain\Notifications\Notification;
use App\StorableEvents\EntityCreated;

class NotificationCreated extends EntityCreated
{
    public function getEntity(): string
    {
        return Notification::class;
    }
}
