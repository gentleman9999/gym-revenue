<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Events;

use App\Domain\Notifications\Notification;
use App\StorableEvents\EntityTrashed;

class NotificationDismissed extends EntityTrashed
{
    public function __construct(public readonly string $id)
    {
        parent::__construct();
    }

    public function getEntity(): string
    {
        return Notification::class;
    }
}
