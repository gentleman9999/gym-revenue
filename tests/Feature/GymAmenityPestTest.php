<?php

declare(strict_types=1);

use App\Domain\Clients\Projections\Client;
use App\Domain\GymAmenities\Actions\CreateGymAmenity;
use App\Domain\GymAmenities\Actions\DeleteGymAmenity;
use App\Domain\GymAmenities\Actions\RestoreGymAmenity;
use App\Domain\GymAmenities\Actions\TrashGymAmenity;
use App\Domain\GymAmenities\Actions\UpdateGymAmenity;
use App\Domain\GymAmenities\Events\GymAmenityCreated;
use App\Domain\GymAmenities\Events\GymAmenityUpdated;
use App\Domain\GymAmenities\Projections\GymAmenity;
use App\Domain\Locations\Actions\CreateLocation;
use App\Domain\Locations\Projections\Location;

beforeEach(function () {
    //
});

function createGymAmenity(array $attributes = []): GymAmenity
{
    $client_id = Client::factory()->create()->id;
    $location_id = createLocation::run(Location::factory()->raw(['client_id' => $client_id]))->id;

    return CreateGymAmenity::run($attributes + [
            'client_id' => $client_id,
            'location_id' => $location_id,
        ]);
}


it('should update gym amenity name on UpdateGymAmenity action', function () {
    $new_name = 'Weights';
    $old_name = 'Massages';

    $gym_amenity = createGymAmenity(['name' => $old_name,'capacity' => 20,'working_hour' => 5,]);
    $this->assertEquals($gym_amenity->name, $old_name);
    UpdateGymAmenity::run($gym_amenity, ['name' => $new_name,]);
    $gym_amenity->refresh();

    $this->assertEquals($gym_amenity->name, $new_name);
});

it('should produce a UpdateGymAmenity event', function () {
    $new_name = 'Weights';
    $old_name = 'Massages';

    $gym_amenity = createGymAmenity(['name' => $old_name]);
    UpdateGymAmenity::run($gym_amenity, ['name' => $new_name,]);
    $gym_amenity->refresh();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(GymAmenityUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should produce a GymAmenityCreated event', function () {
    createGymAmenity(['name' => 'Massages','capacity' => 20,'working_hour' => 5,]);
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(GymAmenityCreated::class, array_column($storedEvents, 'event_class'));
});

it('should delete a gym amenity on DeleteGymAmenity action', function () {
    $gym_amenity = createGymAmenity(['name' => 'Massages','capacity' => 20,'working_hour' => 5,]);

    $this->assertEquals(1, GymAmenity::count());

    DeleteGymAmenity::run($gym_amenity->id);

    $this->assertEquals(0, GymAmenity::count());
});


it('should Trash teams by the TrashGymAmenity action', function () {
    $gym_amenity = createGymAmenity(['name' => 'Massages','capacity' => 20,'working_hour' => 5,]);

    $this->assertEquals(null, $gym_amenity->deleted_at);

    TrashGymAmenity::run($gym_amenity->id);
    $gym_amenity->refresh();

    $this->assertNotEquals(null, $gym_amenity->deleted_at);
});

it('should restore GymAmenity on RestoreGymAmenity action', function () {
    $gym_amenity = createGymAmenity(['name' => 'Massages','capacity' => 20,'working_hour' => 5,]);

    $this->assertEquals(null, $gym_amenity->deleted_at);

    TrashGymAmenity::run($gym_amenity->id);
    $gym_amenity->refresh();

    $this->assertNotEquals(null, $gym_amenity->deleted_at);

    RestoreGymAmenity::run($gym_amenity);

    $this->assertEquals(null, $gym_amenity->deleted_at);
});
