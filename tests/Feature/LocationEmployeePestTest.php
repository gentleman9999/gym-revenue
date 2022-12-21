<?php

declare(strict_types=1);

use App\Domain\Clients\Projections\Client;
use App\Domain\Departments\Actions\CreateDepartment;
use App\Domain\LocationEmployees\Actions\CreateLocationEmployee;
use App\Domain\LocationEmployees\Actions\DeleteLocationEmployee;
use App\Domain\LocationEmployees\Actions\RestoreLocationEmployee;
use App\Domain\LocationEmployees\Actions\TrashLocationEmployee;
use App\Domain\LocationEmployees\Events\LocationEmployeeCreated;
use App\Domain\LocationEmployees\Events\LocationEmployeeRestored;
use App\Domain\LocationEmployees\Events\LocationEmployeeTrashed;
use App\Domain\LocationEmployees\Projections\LocationEmployee;
use App\Domain\Locations\Actions\CreateLocation;
use App\Domain\Locations\Projections\Location;
use App\Domain\Positions\Actions\CreatePosition;
use Tests\Feature\Utilities\UserUtility;

beforeEach(function () {
    //
});

function createLocationEmployee(array $attributes = []): LocationEmployee
{
    $client_id = Client::factory()->create()->id;
    $location = CreateLocation::run(Location::factory()->raw() + [
        'shouldCreateTeam' => true,
        'client_id' => $client_id,
    ]);
    $department_id = CreateDepartment::run([
        'client_id' => $client_id,
        'name' => 'Operations',
    ])->id;
    $attributes['location_id'] = $location->id;
    $attributes['client_id'] = $client_id;
    $attributes['department_id'] = CreateDepartment::run([
        'client_id' => $client_id,
        'name' => 'Operations',
    ])->id;
    $attributes['position_id'] = CreatePosition::run([
        'client_id' => $client_id,
        'name' => 'Front Desk',])->id;
    $attributes['user_id'] = UserUtility::createUserWithoutTeam()->id;
    $attributes['primary_supervisor_user_id'] = $attributes['user_id'];

    return CreateLocationEmployee::run($attributes);
}

it('should produce a LocationEmployeeCreated event', function () {
    createLocationEmployee();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(LocationEmployeeCreated::class, array_column($storedEvents, 'event_class'));
});

it('should delete a location of employee on DeleteLocationEmployee action', function () {
    $location_employee = createLocationEmployee();

    $this->assertEquals(1, LocationEmployee::count());

    DeleteLocationEmployee::run($location_employee->id);

    $this->assertEquals(0, LocationEmployee::count());
});

it('should delete event', function () {
    $location_employee = createLocationEmployee();
    DeleteLocationEmployee::run($location_employee->id);
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(LocationEmployeeCreated::class, array_column($storedEvents, 'event_class'));
});


it('should Trash employee location by the TrashLocation action', function () {
    $location_employee = createLocationEmployee();

    $this->assertEquals(null, $location_employee->deleted_at);

    TrashLocationEmployee::run($location_employee->id);
    $location_employee->refresh();

    $this->assertNotEquals(null, $location_employee->deleted_at);
});

it('should produce a Trash event', function () {
    $location_employee = createLocationEmployee();
    TrashLocationEmployee::run($location_employee->id);
    LocationEmployee::withTrashed()->findOrFail($location_employee->id);
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(LocationEmployeeTrashed::class, array_column($storedEvents, 'event_class'));
});

it('should restore Location on RestoreLocation action', function () {
    $location_employee = createLocationEmployee();
    $this->assertEquals(null, $location_employee->deleted_at);

    TrashLocationEmployee::run($location_employee->id);
    $location_employee->refresh();

    $this->assertNotEquals(null, $location_employee->deleted_at);

    RestoreLocationEmployee::run($location_employee);

    $this->assertEquals(null, $location_employee->deleted_at);
});

it('should produce a restore event', function () {
    $location_employee = createLocationEmployee();

    RestoreLocationEmployee::run($location_employee);

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(LocationEmployeeRestored::class, array_column($storedEvents, 'event_class'));
});
