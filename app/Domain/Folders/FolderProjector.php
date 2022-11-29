<?php

namespace App\Domain\Folders;

use App\Domain\Folders\Events\FolderCreated;
use App\Domain\Folders\Events\FolderDeleted;
use App\Domain\Folders\Events\FolderSharingUpdated;
use App\Domain\Folders\Events\FolderTrashed;
use App\Domain\Folders\Events\FolderUpdated;
use App\Models\Folder;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class FolderProjector extends Projector
{
    public function onFolderCreated(FolderCreated $event): void
    {
        $folder = new Folder();
        //get only the keys we care about (the ones marked as fillable)
        $team_fillable_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new Folder())->getFillable());
        }, ARRAY_FILTER_USE_KEY);
        $folder->fill($team_fillable_data);
        $folder->id = $event->aggregateRootUuid();
        $folder->client_id = $event->payload['client_id'] ?? null;
        $folder->save();
    }

    public function onFolderUpdated(FolderUpdated $event)
    {
        Folder::findOrFail($event->payload['id'])->updateOrFail($event->payload);
    }

    public function onFolderSharingUpdated(FolderSharingUpdated $event)
    {
        Folder::findOrFail($event->payload['id'])->updateOrFail($event->payload);
    }

    public function onFolderDeleted(FolderDeleted $event): void
    {
        Folder::findOrFail($event->aggregateRootUuid())->forceDelete();
    }

    public function onFolderTrashed(FolderTrashed $event): void
    {
        Folder::findOrFail($event->aggregateRootUuid())->deleteOrFail();
    }
}
