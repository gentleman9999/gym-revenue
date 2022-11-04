<?php

declare(strict_types=1);

use App\Domain\Locations\Actions\CreateLocation;
use App\Domain\Locations\Actions\DeleteLocation;
use App\Domain\Locations\Actions\RestoreLocation;
use App\Domain\Locations\Actions\TrashLocation;
use App\Domain\Locations\Actions\UpdateLocation;
use App\Domain\Locations\Events\LocationCreated;
use App\Domain\Locations\Events\LocationUpdated;
use App\Domain\Locations\Projections\Location;

beforeEach(function () {
    //
});

function createLocation(array $attributes = []): Location
{
    return CreateLocation::run($attributes + Location::factory()->raw() + ['shouldCreateTeam' => true]);
}


it('should update location name on UpdateLocation action', function () {
    $newName = fake()->unique()->streetName;
    $oldName = fake()->unique()->streetName;

    $location = \createLocation(['address1' => $oldName]);
    $this->assertEquals($location->address1, $oldName);
    UpdateLocation::run($location, ['address1' => $newName,]);
    $location->refresh();

    $this->assertEquals($location->address1, $newName);
});

it('should produce a UpdateLocation event', function () {
    $newName = fake()->unique()->streetName;
    $oldName = fake()->unique()->streetName;

    $location = \createLocation(['address1' => $oldName]);
    UpdateLocation::run($location, ['address1' => $newName,]);
    $location->refresh();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(LocationUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should produce a LocationCreated event', function () {
    createLocation();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(LocationCreated::class, array_column($storedEvents, 'event_class'));
});

it('should delete a location on DeleteLocation action', function () {
    $location = createLocation();

    $this->assertEquals(1, Location::count());

    DeleteLocation::run($location);

    $this->assertEquals(0, Location::count());
});


it('should Trash teams by the TrashLocation action', function () {
    $location = createLocation();

    $this->assertEquals(null, $location->deleted_at);

    TrashLocation::run($location);
    $location->refresh();

    $this->assertNotEquals(null, $location->deleted_at);
});

it('should restore Location on RestoreLocation action', function () {
    $location = createLocation();

    $this->assertEquals(null, $location->deleted_at);

    TrashLocation::run($location);
    $location->refresh();

    $this->assertNotEquals(null, $location->deleted_at);

    RestoreLocation::run($location);

    $this->assertEquals(null, $location->deleted_at);
});
