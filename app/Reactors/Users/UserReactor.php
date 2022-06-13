<?php

namespace App\Reactors\Users;

use App\Imports\UsersImport;
use App\Imports\UsersImportWithHeader;
use App\Mail\Users\NewUserWelcomeEmail;
use App\Models\User;
use App\Notifications\GymRevNotification;
use App\StorableEvents\Users\Notifications\NotificationCreated;
use App\StorableEvents\Users\WelcomeEmailSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
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

    public function onUserImported(\App\Domain\Users\Events\UsersImported $event)
    {
        $headings = (new HeadingRowImport())->toArray($event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        if (in_array($headings[0][0][0], (new User())->getFillable())) {
            Excel::import(new UsersImportWithHeader($event->client), $event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        } else {
            Excel::import(new UsersImport($event->client), $event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        }
    }
}
