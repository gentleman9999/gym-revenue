<?php

namespace App\Domain\CalendarEvents;

use App\Domain\CalendarEvents\Events\CalendarEventCreated;
use App\Domain\Reminders\Actions\CreateReminder;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class CalendarEventsReactor extends Reactor
{
    public function onCalenderEventCreated(CalendarEventCreated $event): void
    {
        if (array_key_exists('call_task', $event->payload)) {
            if ($event->payload['call_task']) {
                CreateReminder::run([
                    'entity_type' => CalendarEvent::class,
                    'entity_id' => $event->aggregateRootUuid(),
                    'user_id' => $event->payload['owner_id'],
                    'name' => 'Call Task Reminder',
                    'remind_time' => 30,
                ]);
            }
        }
    }
}
