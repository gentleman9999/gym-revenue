<?php

namespace App\Projectors\Clients;

use App\Domain\Users\Models\User;
use App\Models\File;
use App\StorableEvents\Clients\Files\FileCreated;
use App\StorableEvents\Clients\Files\FileDeleted;
use App\StorableEvents\Clients\Files\FileFolderUpdated;
use App\StorableEvents\Clients\Files\FilePermissionsUpdated;
use App\StorableEvents\Clients\Files\FileRenamed;
use App\StorableEvents\Clients\Files\FileRestored;
use App\StorableEvents\Clients\Files\FileTrashed;
use Illuminate\Support\Facades\Storage;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class FileProjector extends Projector
{
    public function onFileCreated(FileCreated $event)
    {
        //get only the keys we care about (the ones marked as fillable)
        $file_table_data = array_filter($event->data, function ($key) {
            return in_array($key, (new File())->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        $file = new File();

        if ($event->user !== null) {
            $user = User::find($event->user);
            $file->user_id = $user->id;
            $file->client_id = $user->client_id;
        }

        $file->fill($file_table_data);
        //TODO: consider moving this to reactor?
        $file->url = Storage::disk('s3')->url($file->key);
        $file->save();
    }

    public function onFileRenamed(FileRenamed $event)
    {
        File::withTrashed()->findOrFail($event->aggregateRootUuid())->updateOrFail(['filename' => $event->data['filename']]);
    }

    public function onFilePermissionsUpdated(FilePermissionsUpdated $event)
    {
        File::withTrashed()->findOrFail($event->aggregateRootUuid())->updateOrFail(['permissions' => $event->data['permissions']]);
    }

    public function onFileFolderUpdated(FileFolderUpdated $event)
    {
        File::withTrashed()->findOrFail($event->aggregateRootUuid())->updateOrFail(['folder' => $event->data['folder']]);
    }

    public function onFileTrashed(FileTrashed $event)
    {
        File::findOrFail($event->id)->deleteOrFail();
    }

    public function onFileRestored(FileRestored $event)
    {
        File::withTrashed()->findOrFail($event->id)->restore();
    }

    public function onFileDeleted(FileDeleted $event)
    {
        File::withTrashed()->findOrFail($event->id)->forceDelete();
    }
}
