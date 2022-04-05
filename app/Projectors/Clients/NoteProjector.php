<?php

namespace App\Projectors\Clients;

use App\Models\ReadReceipt;
use App\StorableEvents\Clients\Note\ReadReceiptCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class NoteProjector extends Projector
{
    public function onReadReceiptCreated(ReadReceiptCreated $event)
    {
        ReadReceipt::create($event->data);
    }
}
