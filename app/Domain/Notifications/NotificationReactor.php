<?php

declare(strict_types=1);

namespace App\Domain\Notifications;

use App\Domain\Notifications\Events\NotificationCreated;
use App\Domain\Users\Models\User;
use App\Notifications\GymRevNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class NotificationReactor extends Reactor implements ShouldQueue
{
    public function onNotificationCreated(NotificationCreated $event): void
    {
        /** @var User $user */
        $user = User::with('contact_preference')->findOrFail($event->aggregateRootUuid());
        $user->notify(new GymRevNotification($user->id, $event->payload));
    }
}
