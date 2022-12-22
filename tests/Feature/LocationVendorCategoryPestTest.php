<?php

declare(strict_types=1);

use App\Domain\Clients\Projections\Client;
use App\Domain\LocationVendorCategories\Actions\CreateLocationVendorCategory;
use App\Domain\LocationVendorCategories\Actions\DeleteLocationVendorCategory;
use App\Domain\LocationVendorCategories\Actions\RestoreLocationVendorCategory;
use App\Domain\LocationVendorCategories\Actions\TrashLocationVendorCategory;
use App\Domain\LocationVendorCategories\Actions\UpdateLocationVendorCategory;
use App\Domain\LocationVendorCategories\Events\LocationVendorCategoryCreated;
use App\Domain\LocationVendorCategories\Events\LocationVendorCategoryUpdated;
use App\Domain\LocationVendorCategories\Projections\LocationVendorCategory;

beforeEach(function () {
    //
});

function createLocationVendorCategory(array $attributes = []): LocationVendorCategory
{
    return CreateLocationVendorCategory::run($attributes + [
        'client_id' => Client::factory()->create()->id,
    ]);
}


it('should update locationVendorCategory name on UpdateLocationVendorCategory action', function () {
    $newName = 'Vendor Category 2';
    $oldName = 'Vendor Category 1';

    $location_vendor_category = createLocationVendorCategory(['name' => $oldName]);
    $this->assertEquals($location_vendor_category->name, $oldName);
    UpdateLocationVendorCategory::run($location_vendor_category, ['name' => $newName,]);
    $location_vendor_category->refresh();

    $this->assertEquals($location_vendor_category->name, $newName);
});

it('should produce a UpdateLocationVendorCategory event', function () {
    $newName = 'Vendor Category 2';
    $oldName = 'Vendor Category 1';

    $location_vendor_category = createLocationVendorCategory(['name' => $oldName]);
    UpdateLocationVendorCategory::run($location_vendor_category, ['name' => $newName,]);
    $location_vendor_category->refresh();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(LocationVendorCategoryUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should produce a LocationVendorCategoryCreated event', function () {
    createLocationVendorCategory(['name' => 'Vendor Category 1']);
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(LocationVendorCategoryCreated::class, array_column($storedEvents, 'event_class'));
});

it('should delete a locationVendorCategory on DeleteLocationVendorCategory action', function () {
    $location_vendor_category = createLocationVendorCategory(['name' => 'Vendor Category 1']);

    $this->assertEquals(1, LocationVendorCategory::count());

    DeleteLocationVendorCategory::run($location_vendor_category);

    $this->assertEquals(0, LocationVendorCategory::count());
});


it('should Trash teams by the TrashLocationVendorCategory action', function () {
    $location_vendor_category = createLocationVendorCategory(['name' => 'Vendor Category 1']);

    $this->assertEquals(null, $location_vendor_category->deleted_at);

    TrashLocationVendorCategory::run($location_vendor_category);
    $location_vendor_category->refresh();

    $this->assertNotEquals(null, $location_vendor_category->deleted_at);
});

it('should restore LocationVendorCategory on RestoreLocationVendorCategory action', function () {
    $location_vendor_category = createLocationVendorCategory(['name' => 'Vendor Category 1']);

    $this->assertEquals(null, $location_vendor_category->deleted_at);

    TrashLocationVendorCategory::run($location_vendor_category);
    $location_vendor_category->refresh();

    $this->assertNotEquals(null, $location_vendor_category->deleted_at);

    RestoreLocationVendorCategory::run($location_vendor_category);

    $this->assertEquals(null, $location_vendor_category->deleted_at);
});
