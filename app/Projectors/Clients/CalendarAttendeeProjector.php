<?php

namespace App\Projectors\Clients;

use App\Actions\Users\Reminders\CreateReminder;
use App\Actions\Users\Reminders\DeleteReminderWithoutID;
use App\Models\Calendar\CalendarAttendee;
use App\Models\Calendar\CalendarEvent;
use App\Models\User;
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
        if ($event->data['entity_type'] == User::class) {
            CreateReminder::run([
                'entity_type' => CalendarEvent::class,
                'entity_id' => $event->data['calendar_event_id'],
                'user_id' => $event->data['entity_id'],
                'name' => 'Default Reminder',
                'remind_time' => 30,
            ]);
        }
    }

    public function onCalendarAttendeeDeleted(CalendarAttendeeDeleted $event)
    {
        CalendarAttendee::whereEntityType($event->data['entity_type'])->whereEntityId($event->data['entity_id'])->forceDelete();
        if ($event->data['entity_type'] == User::class) {
            DeleteReminderWithoutID::run([
                'entity_type' => CalendarEvent::class,
                'user_id' => $event->data['entity_id'],
                'entity_id' => $event->data['event_id'],
            ]);
        }
    }

    public function onCalendarInviteAccepted(CalendarInviteAccepted $event)
    {
        CalendarAttendee::whereId($event->data['attendeeData']['id'])->update(['invitation_status' => 'Accepted']);
    }

    public function onCalendarInviteDeclined(CalendarInviteDeclined $event)
    {
        CalendarAttendee::whereId($event->data['attendeeData']['id'])->update(['invitation_status' => 'Declined']);
    }
}
