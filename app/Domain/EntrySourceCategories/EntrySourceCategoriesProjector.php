<?php

declare(strict_types=1);

namespace App\Domain\EntrySourceCategories;

use App\Domain\EntrySourceCategories\Events\EntrySourceCategoryCreated;
use App\Domain\EntrySourceCategories\Events\EntrySourceCategoryDeleted;
use App\Domain\EntrySourceCategories\Events\EntrySourceCategoryRestored;
use App\Domain\EntrySourceCategories\Events\EntrySourceCategoryTrashed;
use App\Domain\EntrySourceCategories\Events\EntrySourceCategoryUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EntrySourceCategoriesProjector extends Projector
{
    public function onEntrySourceCategoryCreated(EntrySourceCategoryCreated $event): void
    {
        $entry_source = (new EntrySourceCategory())->writeable();
        $entry_source->id = $event->aggregateRootUuid();
        $entry_source->client_id = $event->payload['client_id'];
        $entry_source->fill($event->payload);
        $entry_source->save();
    }

    public function onEntrySourceUpdated(EntrySourceCategoryUpdated $event): void
    {
        EntrySourceCategory::findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($event->payload);
    }

    public function onEntrySourceDeleted(EntrySourceCategoryDeleted $event): void
    {
        EntrySourceCategory::findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }

    public function onEntrySourceTrashed(EntrySourceCategoryTrashed $event): void
    {
        EntrySourceCategory::findOrFail($event->aggregateRootUuid())->writeable()->deleteOrFail();
    }

    public function onEntrySourceRestored(EntrySourceCategoryRestored $event): void
    {
        EntrySourceCategory::findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }
}
