<?php

declare(strict_types=1);

namespace App\Domain\LocationVendors;

use App\Domain\LocationVendors\Events\LocationVendorCreated;
use App\Domain\LocationVendors\Events\LocationVendorDeleted;
use App\Domain\LocationVendors\Events\LocationVendorRestored;
use App\Domain\LocationVendors\Events\LocationVendorTrashed;
use App\Domain\LocationVendors\Events\LocationVendorUpdated;
use App\Domain\LocationVendors\Projections\LocationVendor;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LocationVendorProjector extends Projector
{
    public function onStartingEventReplay(): void
    {
        LocationVendor::truncate();
    }

    public function onLocationVendorCreated(LocationVendorCreated $event): void
    {
        $location_vendor            = new LocationVendor();
        $location_vendor->id        = $event->aggregateRootUuid();
        $location_vendor->client_id = $event->clientId();
        $location_vendor->fill($event->payload);
        $location_vendor->writeable()->save();
    }

    public function onLocationVendorUpdated(LocationVendorUpdated $event): void
    {
        $location_vendor_rows = (new LocationVendor())->getFillable();

        //get only the keys we care about (the ones marked as fillable)
        $location_vendor_table_data = array_filter($event->payload, function ($key) use ($location_vendor_rows) {
            return in_array($key, $location_vendor_rows);
        }, ARRAY_FILTER_USE_KEY);

        LocationVendor::findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($location_vendor_table_data);
    }

    public function onLocationVendorTrashed(LocationVendorTrashed $event): void
    {
        LocationVendor::findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onLocationVendorRestored(LocationVendorRestored $event): void
    {
        LocationVendor::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onLocationVendorDeleted(LocationVendorDeleted $event): void
    {
        LocationVendor::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }
}
