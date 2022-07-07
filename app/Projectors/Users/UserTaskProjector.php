<?php

namespace App\Projectors\Users;

use App\Models\Tasks;
use App\StorableEvents\Clients\Tasks\TaskCreated;
use App\StorableEvents\Clients\Tasks\TaskMarkedIncomplete;
use App\StorableEvents\Clients\Tasks\TaskTrashed;
use App\StorableEvents\Clients\Tasks\TaskUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserTaskProjector extends Projector
{
    public function onTaskCreated(TaskCreated $event)
    {
        Tasks::create($event->data);
    }

    public function onTaskUpdated(TaskUpdated $event)
    {
        Tasks::findOrFail($event->data['id'])->update($event->data);
    }

    public function onTaskDeleted(TaskDeleted $event)
    {
        Tasks::withTrashed()->findOrFail($event->data['id'])->forceDelete();
    }

    public function onTaskRestored(TaskRestored $event)
    {
        Tasks::withTrashed()->findOrFail($event->data['id'])->restore();
    }

    public function onTaskTrashed(TaskTrashed $event)
    {
        Tasks::findOrFail($event->data['id'])->delete();
    }

    public function onTaskMarkedComplete(TaskMarkedComplete $event)
    {
        Tasks::findOrFail($event->data['id'])->update(['completed_at' => $event->created_at]);
    }

    public function onTaskMarkedIncomplete(TaskMarkedIncomplete $event)
    {
        Tasks::findOrFail($event->data['id'])->update(['completed_at' => null]);
    }
}
