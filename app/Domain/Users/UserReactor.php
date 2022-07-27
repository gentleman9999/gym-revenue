<?php

namespace App\Domain\Users;

use App\Domain\Users\Events\UsersImported;
use App\Domain\Users\Models\User;
use App\Imports\UsersImport;
use App\Imports\UsersImportWithHeader;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class UserReactor extends Reactor implements ShouldQueue
{
//    public function onWelcomeEmailSent(WelcomeEmailSent $event)
//    {
//        $user = User::find($event->user);
//        Mail::to($user->email)->send(new NewUserWelcomeEmail($user));
//    }
//

    public function onUserImported(UsersImported $event): void
    {
        $headings = (new HeadingRowImport())->toArray($event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        if (in_array($headings[0][0][0], (new User())->getFillable())) {
            Excel::import(new UsersImportWithHeader($event->client), $event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        } else {
            Excel::import(new UsersImport($event->clientId()), $event->key, 's3', \Maatwebsite\Excel\Excel::CSV);
        }
    }
}
