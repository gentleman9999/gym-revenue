<?php

namespace App\Reactors\Clients;


use App\Actions\Users\Notifications\CreateNotification;
use App\Models\Reminder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class ReminderReactor extends Reactor implements ShouldQueue
{
    public function onReminderTriggered(ReminderTriggered $event)
    {
        $reminder = Reminder::findOrFail($event->id);
        $entity = null;
        if ($reminder->entity) {
            $entity = $reminder->entity::findOrFail($reminder->entity_id);
        }
        CreateNotification::run([
            'user_id' => $reminder->user_id,
            'state' => 'warning',
            'text' => "Reminder",
            //TODO:we prob dont need to store text, since the UI should be build
            // the the view based on entity_type and the entity's data
            'misc' => [
                'remind_time' => $reminder->remind_time,
                'entity' => $entity->toArray(),
            ]
        ]);
    }
}
