<?php

declare(strict_types=1);

namespace App\Domain\StructuredDocuments\StructuredDocumentFiles;

use App\Domain\StructuredDocuments\StructuredDocumentFiles\Events\StructuredDocumentFileCreated;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Events\StructuredDocumentFileDeleted;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Events\StructuredDocumentFileRestored;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Events\StructuredDocumentFileTrashed;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Events\StructuredDocumentFileUpdated;
use App\Domain\StructuredDocuments\StructuredDocumentFiles\Projections\StructuredDocumentFile;
use Illuminate\Support\Facades\DB;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class StructuredDocumentFileProjector extends Projector
{
    public function onStartingEventReplay(): void
    {
        StructuredDocumentFile::delete();
    }

    public function onStructuredDocumentFileCreated(StructuredDocumentFileCreated $event): void
    {
        DB::transaction(function () use ($event): void {
            $structured_document_file = (new StructuredDocumentFile())->writeable();
            $structured_document_file->fill($event->payload);
            $structured_document_file->id = $event->aggregateRootUuid();
            $structured_document_file->save();
        });
    }

    public function onStructuredDocumentFileDeleted(StructuredDocumentFileDeleted $event): void
    {
        StructuredDocumentFile::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onStructuredDocumentFileRestored(StructuredDocumentFileRestored $event): void
    {
        StructuredDocumentFile::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onStructuredDocumentFileTrashed(StructuredDocumentFileTrashed $event): void
    {
        StructuredDocumentFile::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onStructuredDocumentFileUpdated(StructuredDocumentFileUpdated $event): void
    {
        StructuredDocumentFile::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->fill($event->payload)->save();
    }
}
