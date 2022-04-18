<?php

namespace App\Reactors\Clients;

use App\Models\File;
use App\StorableEvents\Clients\Files\FileCreated;
use App\StorableEvents\Clients\Files\FileDeleted;
use App\StorableEvents\Clients\Files\FileReplaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class FileReactor extends Reactor implements ShouldQueue
{

    public function onFileCreated(FileCreated $event){
        $file = File::find($event->data['id']);
        if(!empty($file)){
            $destKey = "{$event->data['id']}/{$event->data['id']}";
            if($file->user_id){
                $destKey = "{$file->client_id}/{$file->user_id}/{$event->data['id']}";
            }
            Storage::disk('s3')->move($event->data['key'], $destKey);
            $file->key = $destKey;
            $file->url = Storage::disk('s3')->url($destKey);
            $file->save();
        }
    }
    public function onFileDeleted(FileDeleted $event)
    {
        Storage::disk('s3')->delete($event->data['key']);
    }
//@TODO: we should create thumbnails for image types somewhere
}
