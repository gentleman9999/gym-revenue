<?php

namespace App\Domain\Reminders;

use App\Domain\Notifications\Actions\CreateNotification;
use App\Domain\Reminders\Events\ReminderTriggered;
use App\Domain\Users\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ReminderReactor extends Reactor implements ShouldQueue
{
    public function onReminderTriggered(ReminderTriggered $event): void
    {
//        dd($event);
//        Logger::debug($event);
        $reminder = Reminder::findOrFail($event->id);
        $entity = null;
        if ($reminder->entity_type) {
            $entity = $reminder->entity_type::findOrFail($reminder->entity_id);
        }
        CreateNotification::run([
            'user_id' => $reminder->user_id,
            'state' => 'warning',
            'text' => "Reminder",
            'entity_type' => $reminder->entity_type,
            'entity' => $entity ,
            'type' => 'CALENDAR_EVENT_REMINDER',
            //TODO:we prob dont need to store text, since the UI should be build
            // the the view based on entity_type and the entity's data
            'misc' => [
                'remind_time' => $reminder->remind_time,
            ],
        ], User::find($reminder->user_id));
    }
}
