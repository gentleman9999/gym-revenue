<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments;

use App\Domain\StructuredDocuments\Events\StructuredDocumentCreated;
use App\Domain\StructuredDocuments\Events\StructuredDocumentDeleted;
use App\Domain\StructuredDocuments\Events\StructuredDocumentRestored;
use App\Domain\StructuredDocuments\Events\StructuredDocumentTrashed;
use App\Domain\StructuredDocuments\Events\StructuredDocumentUpdated;
use App\Domain\StructuredDocuments\Projections\StructuredDocument;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class StructuredDocumentProjector extends Projector
{
    public function onStartingEventReplay()
    {
        StructuredDocument::delete();
    }

    public function onStructuredDocumentCreated(StructuredDocumentCreated $event): void
    {
        DB::transaction(function () use ($event) {
            $structured_document = (new StructuredDocument())->writeable();
            $structured_document->fill($event->payload);
            $structured_document->id = $event->aggregateRootUuid();
            $structured_document->save();
        });
    }

    public function onStructuredDocumentDeleted(StructuredDocumentDeleted $event): void
    {
        StructuredDocument::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onStructuredDocumentRestored(StructuredDocumentRestored $event): void
    {
        StructuredDocument::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onStructuredDocumentTrashed(StructuredDocumentTrashed $event): void
    {
        StructuredDocument::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onStructuredDocumentUpdated(StructuredDocumentUpdated $event): void
    {
        StructuredDocument::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }
}
