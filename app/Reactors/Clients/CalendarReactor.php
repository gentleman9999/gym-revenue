<?php

namespace App\Reactors\Clients;

use App\Actions\Clients\Calendar\InviteAttendeeEmail;
use App\Models\Utility\AppState;
use App\StorableEvents\Clients\Calendar\CalendarAttendeeAdded;
use App\StorableEvents\Clients\Calendar\CalendarAttendeeInvited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mailgun\Mailgun;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class CalendarReactor extends Reactor implements ShouldQueue
{
    public function onCalendarAttendeeAdded(CalendarAttendeeAdded $event)
    {
        if (array_key_exists('is_task', $event->data)) {
            if (! $event->data['is_task']) {
                if (! AppState::isSimuationMode()) {
                    InviteAttendeeEmail::run($event);
                }
            }
        } else {
            if (! AppState::isSimuationMode()) {
                InviteAttendeeEmail::run($event);
            }
        }
    }

    public function onCalendarAttendeeInvited(CalendarAttendeeInvited $event)
    {
        $mg = Mailgun::create(env('MAILGUN_SECRET'));
        $mg->messages()->send(env('MAILGUN_DOMAIN'), [
            'from' => env('MAIL_FROM_ADDRESS'),
            'to' => $event->data['entity_data']['email'],
            'subject' => $event->data['subject'],
            'html' => $event->data['body'],
        ]);
    }
}
