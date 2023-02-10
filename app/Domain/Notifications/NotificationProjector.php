<?php

declare(strict_types=1);

namespace App\Domain\Notifications;

use App\Domain\Notifications\Events\NotificationCreated;
use App\Domain\Notifications\Events\NotificationDismissed;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class NotificationProjector extends Projector
{
    public function onNotificationCreated(NotificationCreated $event): void
    {
        $notification          = (new Notification())->writeable();
        $notification->id      = $event->payload['id'];
        $notification->user_id = $event->aggregateRootUuid();
        $notification->fill($event->payload);
        $notification->save();
    }

    public function onNotificationDismissed(NotificationDismissed $event): void
    {
        Notification::find($event->id)->writeable()->delete();
    }
}
