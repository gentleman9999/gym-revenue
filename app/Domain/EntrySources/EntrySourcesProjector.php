<?php

declare(strict_types=1);

namespace App\Domain\EntrySources;

use App\Domain\EntrySources\Events\EntrySourceCreated;
use App\Domain\EntrySources\Events\EntrySourceUpdated;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class EntrySourcesProjector extends Projector
{
    public function onEntrySourceCreated(EntrySourceCreated $event): void
    {
        $entry_source            = (new EntrySource())->writeable();
        $entry_source->id        = $event->aggregateRootUuid();
        $entry_source->client_id = $event->payload['client_id'];
        $entry_source->fill($event->payload);
        $entry_source->save();
    }

    public function onEntrySourceUpdated(EntrySourceUpdated $event): void
    {
        $client_entry_sources = EntrySource::whereClientId($event->clientId())->get();
        foreach ($client_entry_sources as $client_entry_source) {
            $client_entry_source                          = (new EntrySource())->whereId($client_entry_source->id)->first()->writeable();
            $client_entry_source->is_default_entry_source = false;
            $client_entry_source->save();
        }
        $entry_source = EntrySource::findOrFail($event->aggregateRootUuid())->writeable();
        $entry_source->fill($event->payload);
        $entry_source->save();
    }
}
