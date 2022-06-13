<?php

namespace App\Domain\Notifications;

use App\Domain\Notifications\Events\NotificationCreated;
use App\Models\User;
use App\Notifications\GymRevNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class NotificationReactor extends Reactor implements ShouldQueue
{
    public function onNotificationCreated(NotificationCreated $event)
    {
//        \App\Events\NotificationCreated::dispatch($event->user, $event->data);
        $user_id = $event->aggregateRootUuid();
        $user = User::with('contact_preference')->findOrFail($user_id);
        $user->notify(new GymRevNotification($user_id, $event->payload));

        if ($user->contact_preference->value == 'sms') {
        } else {
        }
    }
}
