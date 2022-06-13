<?php

namespace App\Reactors\Users;

use App\Domain\Notifications\Events\NotificationCreated;
use App\Mail\Users\NewUserWelcomeEmail;
use App\Models\User;
use App\Notifications\GymRevNotification;
use App\StorableEvents\Users\WelcomeEmailSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class UserReactor extends Reactor implements ShouldQueue
{
    public function onWelcomeEmailSent(WelcomeEmailSent $event)
    {
        $user = User::find($event->user);
        Mail::to($user->email)->send(new NewUserWelcomeEmail($user));
    }

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
