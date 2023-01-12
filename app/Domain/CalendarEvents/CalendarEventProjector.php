<?php

namespace App\Domain\CalendarEvents;

use App\Domain\CalendarAttendees\CalendarAttendee;
use App\Domain\CalendarEvents\Events\CalendarEventCreated;
use App\Domain\CalendarEvents\Events\CalendarEventDeleted;
use App\Domain\CalendarEvents\Events\CalendarEventNotified;
use App\Domain\CalendarEvents\Events\CalendarEventRestored;
use App\Domain\CalendarEvents\Events\CalendarEventTrashed;
use App\Domain\CalendarEvents\Events\CalendarEventUpdated;
use App\Domain\CalendarEventTypes\CalendarEventType;
use App\Domain\Reminders\Reminder;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CalendarEventProjector extends Projector
{
    public function onStartingEventReplay()
    {
        CalendarEvent::truncate();
        CalendarEventType::truncate();
        CalendarAttendee::truncate();
        Reminder::truncate();
    }

    public function onCalenderEventCreated(CalendarEventCreated $event): void
    {
        $calendarEvent = (new CalendarEvent())->writeable();

        $calendar_event_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new CalendarEvent())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $calendarEvent->forceFill(['client_id' => $event->clientId(), 'id' => $event->aggregateRootUuid()]);
        $calendarEvent->fill($calendar_event_table_data);
        $calendarEvent->save();
    }

    public function onCalenderEventUpdated(CalendarEventUpdated $event): void
    {
        CalendarEvent::findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
    }

    public function onCalenderEventTrashed(CalendarEventTrashed $event): void
    {
        CalendarEvent::findOrFail($event->aggregateRootUuid())->writeable()->deleteOrFail();
    }

    public function onCalenderEventRestored(CalendarEventRestored $event): void
    {
        CalendarEvent::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onCalenderEventDeleted(CalendarEventDeleted $event): void
    {
        CalendarEvent::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onCalendarEventNotified(CalendarEventNotified $event): void
    {
        CalendarEvent::findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail([
            'overdue_reminder_sent' => true,
        ]);
    }
}
