<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocumentRequests;

use App\Domain\StructuredDocumentRequests\Events\StructuredDocumentRequestCreated;
use App\Domain\StructuredDocumentRequests\Events\StructuredDocumentRequestDeleted;
use App\Domain\StructuredDocumentRequests\Events\StructuredDocumentRequestRestored;
use App\Domain\StructuredDocumentRequests\Events\StructuredDocumentRequestTrashed;
use App\Domain\StructuredDocumentRequests\Events\StructuredDocumentRequestUpdated;
use App\Domain\StructuredDocumentRequests\Projections\StructuredDocumentRequest;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class StructuredDocumentRequestProjector extends Projector
{
    public function onStartingEventReplay()
    {
        StructuredDocumentRequest::delete();
    }

    public function onStructuredDocumentRequestCreated(StructuredDocumentRequestCreated $event): void
    {
        DB::transaction(function () use ($event) {
            $structured_document_request = (new StructuredDocumentRequest())->writeable();
            $structured_document_request->fill($event->payload);
            $structured_document_request->id = $event->aggregateRootUuid();
            $structured_document_request->save();
        });
    }

    public function onStructuredDocumentRequestDeleted(StructuredDocumentRequestDeleted $event): void
    {
        StructuredDocumentRequest::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onStructuredDocumentRequestRestored(StructuredDocumentRequestRestored $event): void
    {
        StructuredDocumentRequest::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onStructuredDocumentRequestTrashed(StructuredDocumentRequestTrashed $event): void
    {
        StructuredDocumentRequest::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onStructuredDocumentRequestUpdated(StructuredDocumentRequestUpdated $event): void
    {
        StructuredDocumentRequest::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }
}
