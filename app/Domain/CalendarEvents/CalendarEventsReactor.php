<?php

namespace App\Domain\CalendarEvents;

use App\Domain\CalendarEvents\Events\CalendarEventCreated;
use App\Domain\CalendarEvents\Events\CalendarEventFileUploaded;
use App\Domain\Reminders\Actions\CreateReminder;
use App\Domain\Users\Models\User;
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

    public function onCalendarEventFileUploaded(CalendarEventFileUploaded $event): void
    {
        $data = $event->payload;
        $model = CalendarEvent::find($data['entity_id']);

        \App\Actions\Clients\Files\CreateFile::run($data, $model, User::find($event->userId()));
    }
}
