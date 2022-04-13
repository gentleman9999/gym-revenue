<?php

namespace App\Reactors\Clients;

use App\Actions\Clients\Calendar\InviteAttendeeEmail;
use App\Actions\Clients\Calendar\InviteAttendeeSMS;
use App\StorableEvents\Clients\Calendar\CalendarAttendeeAdded;
use App\StorableEvents\Clients\Calendar\CalendarAttendeeInvited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mailgun\Mailgun;
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


    public function onCalendarAttendeeInvited(CalendarAttendeeInvited $event)
    {
        $mg = Mailgun::create(env('MAILGUN_SECRET'));
        $mg->messages()->send(env('MAILGUN_DOMAIN'), [
            'from'    => env('MAIL_FROM_ADDRESS'),
            'to'      => 'blair@capeandbay.com',//$event->data['entity_data']['email'],
            'subject' => $event->data['subject'],
            'html'    => $event->data['body'],
        ]);
    }


}
