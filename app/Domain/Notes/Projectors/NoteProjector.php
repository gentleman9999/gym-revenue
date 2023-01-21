<?php

declare(strict_types=1);

namespace App\Domain\Notes\Projectors;

use App\Domain\Notes\Events\NoteCreated;
use App\Domain\Notes\Events\NoteDeleted;
use App\Domain\Notes\Events\NoteTrashed;
use App\Domain\Notes\Events\NoteUpdated;
use App\Domain\Notes\Model\Note;
use App\Models\ReadReceipt;
use App\StorableEvents\Clients\Note\ReadReceiptCreated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class NoteProjector extends Projector
{
    public function onReadReceiptCreated(ReadReceiptCreated $event)
    {
        ReadReceipt::create($event->data);
    }

    public function onNoteCreated(NoteCreated $event)
    {
        $data = [
            'id' => $event->payload['id'],
            'title' => $event->payload['title'],
            'note' => $event->payload['note'],
            'created_by_user_id' => $event->payload['created_by_user_id'],
            'entity_type' => Note::class,
            'entity_id' => $event->payload['id'],
        ];

        if (isset($event->payload['active'])) {
            $data['active'] = $event->payload['active'];
        }

        if (isset($event->payload['entity_type'])) {
            $data['entity_type'] = $event->payload['entity_type'];
        }

        if (isset($event->payload['entity_id'])) {
            $data['entity_id'] = $event->payload['entity_id'];
        }
        if (isset($event->payload['category'])) {
            $data['category'] = $event->payload['category'];
        }

        return (new Note())->fill($data)->writeable()->save();
    }

    public function onNoteDeleted(NoteDeleted $event): void
    {
        Note::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onNoteTrashed(NoteTrashed $event): void
    {
        Note::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onNoteUpdated(NoteUpdated $event): void
    {
        Note::findOrFail($event->payload['id'])->writeable()->fill($event->payload)->save();
    }
}
