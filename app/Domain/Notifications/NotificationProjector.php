<?php

namespace App\Domain\Notifications;

use App\Domain\Notifications\Events\NotificationCreated;
use App\Domain\Notifications\Events\NotificationDismissed;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class NotificationProjector extends Projector
{
    public function onNotificationCreated(NotificationCreated $event)
    {
        (new Notification())->forceFill(array_merge($event->payload, ['user_id' => $event->aggregateRootUuid()]))->save();
//        Notification::create(array_merge($event->payload, ['user_id' => $event->aggregateRootUuid()]));
    }

    public function onNotificationDismissed(NotificationDismissed $event)
    {
        Notification::withTrashed($event->id)->forceFill(['deleted_at' => $event->createdAt()])->save();
    }
}
