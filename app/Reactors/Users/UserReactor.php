<?php

namespace App\Reactors\Users;

use App\Mail\Users\NewUserWelcomeEmail;
use App\Models\User;
use App\Notifications\GymRevNotification;
use App\StorableEvents\Users\Notifications\NotificationCreated;
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
        $user = User::findOrFail($event->user)->with('contact_preference');
        $user->notify(new GymRevNotification($event->user, $event->data));

        if ($user->contact_preference->value == 'sms') {
        } else {
        }
    }
}
