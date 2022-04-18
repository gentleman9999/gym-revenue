<?php

namespace App\Reactors\Users;

use App\Mail\Users\NewUserWelcomeEmail;
use App\Models\Notification;
use App\Models\User;
use App\Notifications\GymRevNotification;
use App\StorableEvents\Users\Notifications\NotificationCreated;
use App\StorableEvents\Users\WelcomeEmailSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
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
        Log::debug($event->data);
        User::findOrFail($event->user)->notify(new GymRevNotification($event->user, $event->data));
//        User::findOrFail($event->user)->notify(new NotificationCreated($event->user, $event->data));

    }
}
