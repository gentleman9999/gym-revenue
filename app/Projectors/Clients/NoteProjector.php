<?php

namespace App\Projectors\Clients;

use App\Models\Note;
use App\Models\ReadReceipt;
use App\StorableEvents\Clients\Note\ReadReceiptCreated;
use App\StorableEvents\Clients\Notes\NoteCreated;
use App\StorableEvents\Clients\Notes\NoteDeleted;
use App\StorableEvents\Clients\Notes\NoteRestored;
use App\StorableEvents\Clients\Notes\NoteTrashed;
use App\StorableEvents\Clients\Notes\NoteUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class NoteProjector extends Projector
{
    public function onReadReceiptCreated(ReadReceiptCreated $event)
    {
        ReadReceipt::create($event->data);
    }

    public function onNoteCreated(NoteCreated $event)
    {
        $note = Note::create(
            [
                'id' => $event->payload['id'],
                'title' => $event->payload['title'],
                'note' => $event->payload['note'],
                'created_by_user_id' => $event->payload['created_by_user_id'],
                //'scope' => $event->payload['client_id'],
            ]
        );
//        foreach ($event->payload['ability_names'] as $ability) {
//            Bouncer::allow($note->name)->to($ability, \App\Models\Note::getEntityFromGroup(substr($ability, 0, strpos($ability, '.'))));
//        }
    }

    public function onNoteUpdated(NoteUpdated $event)
    {
//        $note = Note::note()->findOrFail($event->payload['id']);
        Note::findOrFail($event->payload['id'])->updateOrFail(array_merge($event->payload, ['title' => $event->payload['title']]));
    }

    public function onNoteTrashed(NoteTrashed $event)
    {
        Note::findOrFail($event->id)->delete();
    }

    public function onNoteRestored(NoteRestored $event)
    {
        Note::withTrashed()->findOrFail($event->id)->restore();
    }

    public function onNoteDeleted(NoteDeleted $event)
    {
        Note::findOrFail($event->id)->delete();
    }
}
