<?php

namespace App\Reactors\Clients;

use App\Actions\Clients\Calendar\InviteAttendeeEmail;
use App\Actions\Clients\Calendar\InviteAttendeeSMS;
use App\StorableEvents\Clients\Calendar\CalendarAttendeeAdded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class CalendarReactor extends Reactor implements ShouldQueue
{

    public function onCalendarAttendeeAdded(CalendarAttendeeAdded $event){
        //TODO develop logic to determine user preference for being contacted
        $sendInvites = env('SEND_INVITES');

        if($sendInvites) {
            InviteAttendeeEmail::run($event);

            //InviteAttendeeSMS::run($event);
        } else {

        }

    }


}
