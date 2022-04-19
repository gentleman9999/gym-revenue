<?php

namespace App\Projectors\Clients;

use App\Actions\Users\Reminders\CreateReminder;
use App\Actions\Users\Reminders\DeleteReminder;
use App\Models\Calendar\CalendarAttendee;
use App\Models\Calendar\CalendarEvent;
use App\StorableEvents\Clients\Calendar\CalendarAttendeeAdded;
use App\StorableEvents\Clients\Calendar\CalendarAttendeeDeleted;
use App\StorableEvents\Clients\Calendar\CalendarInviteAccepted;
use App\StorableEvents\Clients\Calendar\CalendarInviteDeclined;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CalendarAttendeeProjector extends Projector
{

    public function onCalendarAttendeeAdded(CalendarAttendeeAdded $event)
    {
        CalendarAttendee::create($event->data);
        CreateReminder::run([
            'entity_type' => CalendarEvent::class,
            'entity_id' => $event->data['calendar_event_id'],
            'user_id' => $event->data['entity_id'],
            'name' => 'Default Reminder',
            'remind_time' => 30,

        ]);
    }

    public function onCalendarAttendeeDeleted(CalendarAttendeeDeleted $event)
    {
        CalendarAttendee::whereEntityType($event->data['entity_type'])->whereEntityId($event->data['entity_id'])->forceDelete();
        //DeleteReminder::run([]);
    }

    public function onCalendarInviteAccepted(CalendarInviteAccepted $event)
    {
        CalendarAttendee::whereId($event->data['attendeeData']['id'])->update(array('invitation_status' => 'Accepted'));
    }

    public function onCalendarInviteDeclined(CalendarInviteDeclined $event)
    {
        CalendarAttendee::whereId($event->data['attendeeData']['id'])->update(array('invitation_status' => 'Declined'));
    }


}

