<?php

declare(strict_types=1);

namespace App\Domain\LocationVendorCategories;

use App\Domain\LocationVendorCategories\Events\LocationVendorCategoryCreated;
use App\Domain\LocationVendorCategories\Events\LocationVendorCategoryDeleted;
use App\Domain\LocationVendorCategories\Events\LocationVendorCategoryRestored;
use App\Domain\LocationVendorCategories\Events\LocationVendorCategoryTrashed;
use App\Domain\LocationVendorCategories\Events\LocationVendorCategoryUpdated;
use App\Domain\LocationVendorCategories\Projections\LocationVendorCategory;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class LocationVendorCategoryProjector extends Projector
{
    public function onStartingEventReplay(): void
    {
        LocationVendorCategory::truncate();
    }

    public function onLocationVendorCategoryCreated(LocationVendorCategoryCreated $event): void
    {
        $vendor_category_rows = (new LocationVendorCategory())->getFillable();

        //get only the keys we care about (the ones marked as fillable)
        $location_vendor_category_table_data = array_filter($event->payload, function ($key) use ($vendor_category_rows) {
            return in_array($key, $vendor_category_rows);
        }, ARRAY_FILTER_USE_KEY);

        $location_vendor_category            = new LocationVendorCategory();
        $location_vendor_category->id        = $event->aggregateRootUuid();
        $location_vendor_category->client_id = $event->clientId();

        $location_vendor_category->fill(
            $location_vendor_category_table_data
        )->writeable()->save();
    }

    public function onLocationVendorCategoryUpdated(LocationVendorCategoryUpdated $event): void
    {
        //get only the keys we care about (the ones marked as fillable)
        $location_vendor_category_table_data = array_filter($event->payload, function ($key) {
            return in_array($key, (new LocationVendorCategory())->getFillable());
        }, ARRAY_FILTER_USE_KEY);

        LocationVendorCategory::findOrFail($event->aggregateRootUuid())->writeable()->updateOrFail($location_vendor_category_table_data);
    }

    public function onLocationVendorCategoryTrashed(LocationVendorCategoryTrashed $event): void
    {
        LocationVendorCategory::findOrFail($event->aggregateRootUuid())->writeable()->delete();
    }

    public function onLocationVendorCategoryRestored(LocationVendorCategoryRestored $event): void
    {
        LocationVendorCategory::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->restore();
    }

    public function onLocationVendorCategoryDeleted(LocationVendorCategoryDeleted $event): void
    {
        LocationVendorCategory::withTrashed()->findOrFail($event->aggregateRootUuid())->writeable()->forceDelete();
    }
}
