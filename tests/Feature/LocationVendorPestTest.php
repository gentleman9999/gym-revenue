<?php

declare(strict_types=1);

use App\Domain\Clients\Projections\Client;
use App\Domain\Locations\Actions\CreateLocation;
use App\Domain\Locations\Projections\Location;
use App\Domain\LocationVendors\Actions\CreateLocationVendor;
use App\Domain\LocationVendors\Actions\DeleteLocationVendor;
use App\Domain\LocationVendors\Actions\RestoreLocationVendor;
use App\Domain\LocationVendors\Actions\TrashLocationVendor;
use App\Domain\LocationVendors\Actions\UpdateLocationVendor;
use App\Domain\LocationVendors\Events\LocationVendorCreated;
use App\Domain\LocationVendors\Events\LocationVendorUpdated;
use App\Domain\LocationVendors\Projections\LocationVendor;

beforeEach(function () {
    //
});

function createLocationVendor(array $attributes = []): LocationVendor
{
    $client_id = Client::factory()->create()->id;
    $location_id = createLocation::run(Location::factory()->raw(['client_id' => $client_id]))->id;

    return CreateLocationVendor::run($attributes + LocationVendor::factory()->raw() + [
        'client_id' => $client_id,
        'location_id' => $location_id,
        'vendor_category_id' => '',
    ]);
}


it('should update locationVendor name on UpdateLocationVendor action', function () {
    $newName = fake()->unique()->name;
    $oldName = fake()->unique()->name;

    $location_vendor = createLocationVendor(['poc_name' => $oldName]);
    $this->assertEquals($location_vendor->poc_name, $oldName);
    UpdateLocationVendor::run($location_vendor, ['poc_name' => $newName,]);
    $location_vendor->refresh();

    $this->assertEquals($location_vendor->poc_name, $newName);
});

it('should produce a UpdateLocationVendor event', function () {
    $newName = fake()->unique()->name;
    $oldName = fake()->unique()->name;

    $location_vendor = createLocationVendor(['poc_name' => $oldName]);
    UpdateLocationVendor::run($location_vendor, ['poc_name' => $newName,]);
    $location_vendor->refresh();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(LocationVendorUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should produce a LocationVendorCreated event', function () {
    createLocationVendor();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(LocationVendorCreated::class, array_column($storedEvents, 'event_class'));
});

it('should delete a locationVendor on DeleteLocationVendor action', function () {
    $location_vendor = createLocationVendor();

    $this->assertEquals(1, LocationVendor::count());

    DeleteLocationVendor::run($location_vendor);

    $this->assertEquals(0, LocationVendor::count());
});


it('should Trash teams by the TrashLocationVendor action', function () {
    $location_vendor = createLocationVendor();

    $this->assertEquals(null, $location_vendor->deleted_at);

    TrashLocationVendor::run($location_vendor);
    $location_vendor->refresh();

    $this->assertNotEquals(null, $location_vendor->deleted_at);
});

it('should restore LocationVendor on RestoreLocationVendor action', function () {
    $location_vendor = createLocationVendor();

    $this->assertEquals(null, $location_vendor->deleted_at);

    TrashLocationVendor::run($location_vendor);
    $location_vendor->refresh();

    $this->assertNotEquals(null, $location_vendor->deleted_at);

    RestoreLocationVendor::run($location_vendor);

    $this->assertEquals(null, $location_vendor->deleted_at);
});
