<?php

declare(strict_types=1);

namespace App\Projectors\Users;

use App\Models\Tasks;
use App\StorableEvents\Clients\Tasks\TaskCreated;
use App\StorableEvents\Clients\Tasks\TaskMarkedIncomplete;
use App\StorableEvents\Clients\Tasks\TaskTrashed;
use App\StorableEvents\Clients\Tasks\TaskUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class UserTaskProjector extends Projector
{
    public function onTaskCreated(TaskCreated $event): void
    {
        Tasks::create($event->data);
    }

    public function onTaskUpdated(TaskUpdated $event): void
    {
        Tasks::findOrFail($event->data['id'])->update($event->data);
    }

    public function onTaskDeleted(TaskDeleted $event): void
    {
        Tasks::withTrashed()->findOrFail($event->data['id'])->forceDelete();
    }

    public function onTaskRestored(TaskRestored $event): void
    {
        Tasks::withTrashed()->findOrFail($event->data['id'])->restore();
    }

    public function onTaskTrashed(TaskTrashed $event): void
    {
        Tasks::findOrFail($event->data['id'])->delete();
    }

    public function onTaskMarkedComplete(TaskMarkedComplete $event): void
    {
        Tasks::findOrFail($event->data['id'])->update(['completed_at' => $event->created_at]);
    }

    public function onTaskMarkedIncomplete(TaskMarkedIncomplete $event): void
    {
        Tasks::findOrFail($event->data['id'])->update(['completed_at' => null]);
    }
}
