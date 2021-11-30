<?php

namespace App\Reactors\Clients;

use App\Actions\Sms\Twilio\FireTwilioMsg;
use App\Mail\EndUser\EmailFromRep;
use App\Models\Endusers\Lead;
use App\Models\File;
use App\StorableEvents\Clients\Files\FileCreated;
use App\StorableEvents\Clients\Files\FileDeleted;
use App\StorableEvents\Clients\Files\FileReplaced;
use App\StorableEvents\Endusers\LeadWasEmailedByRep;
use App\StorableEvents\Endusers\LeadWasTextMessagedByRep;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\EventSourcing\EventHandlers\Reactors\Reactor;

class FileReactor extends Reactor implements ShouldQueue
{

    public function onFileCreated(FileCreated $event){
        $file = File::find($event->fileId);
        if(!empty($file)){
            $destKey = "{$file->client_id}/{$event->fileId}";
            Storage::disk('s3')->move($event->tmpKey, $destKey);
            $file->key = $destKey;
            $file->url = Storage::disk('s3')->url($destKey);
            $file->save();
        }
    }
    public function onFileDeleted(FileDeleted $event)
    {
        Storage::disk('s3')->delete($event->key);
    }
//@TODO: we should create thumbnails for image types somewhere
}
