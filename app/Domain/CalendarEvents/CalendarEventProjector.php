<?php

declare(strict_types=1);

namespace App\Domain\CalendarEvents;

use App\Domain\CalendarAttendees\CalendarAttendee;
use App\Domain\CalendarEvents\Events\CalendarEventCreated;
use App\Domain\CalendarEvents\Events\CalendarEventDeleted;
use App\Domain\CalendarEvents\Events\CalendarEventNotified;
use App\Domain\CalendarEvents\Events\CalendarEventRestored;
use App\Domain\CalendarEvents\Events\CalendarEventTrashed;
use App\Domain\CalendarEvents\Events\CalendarEventUpdated;
use App\Domain\CalendarEventTypes\CalendarEventType;
use App\Domain\Notifications\Actions\CreateNotification;
use App\Domain\Reminders\Reminder;
use App\Domain\Users\Models\User;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CalendarEventProjector extends Projector
{
    public function onStartingEventReplay(): void
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
        CreateNotification::run([
            'user_id' => $event->payload['owner_id'],
            'state' => 'warning',
            'text' => "Task " . $event->payload['title'] . " Overdue!",
            'entity_type' => CalendarEvent::class,
            'entity_id' => $event->payload['id'],
            'entity' => ['start' => $event->payload['start'], 'title' => 'Task ' . $event->payload['title'] . ' Overdue', 'type' => 'TASK_OVERDUE'],
            'type' => 'TASK_OVERDUE',
            'misc' => [
                'remind_time' => 1,
            ],
        ], User::find($event->payload['owner_id']));
        CalendarEvent::findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail([
            'overdue_reminder_sent' => true,
        ]);
    }
}
