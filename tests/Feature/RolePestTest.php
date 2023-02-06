<?php

declare(strict_types=1);

use App\Domain\Clients\Projections\Client;
use App\Domain\Roles\Actions\DeleteRole;
use App\Domain\Roles\Actions\UpdateRole;
use App\Domain\Roles\Events\RoleCreated;
use App\Domain\Roles\Events\RoleDeleted;
use App\Domain\Roles\Events\RoleUpdated;
use App\Domain\Roles\Role;
use Illuminate\Support\Facades\DB;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\Feature\Utilities\UserUtility;

beforeEach(function () {
    //
});

function createRole(array $attributes = []): Role
{
    return UserUtility::createRole($attributes);
}

it('role created stored event should be created', function () {
    \createRole();

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(RoleCreated::class, array_column($storedEvents, 'event_class'));
});

it('should delete a Role', function () {
    $role = \createRole();
    $test = Bouncer::role()->find($role->id)->getAbilities()->toArray();
    $initialCount = DB::table('roles')->count();
    if (Bouncer::role()->find($role->id)) {
        $test1 = Bouncer::role()->find($role->id)->getAbilities()->toArray();
    } else {
        $test1 = null;
    }
    $storedEvents = DB::table('stored_events')->get()->toArray();  // Delete before finishing
    $this->assertEquals(1, $initialCount);
    //The Following fails because the DeleteRole doesn't find the role when retrieving from the DB
    DeleteRole::run($role);
    $finalCount = DB::table('roles')->count();

    $this->assertEquals(0, $finalCount);
});

it('should have a delete role event', function () {
    $role = \createRole();

    //The Following fails because the DeleteRole doesn't find the role when retrieving from the DB
    DeleteRole::run($role);
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(RoleDeleted::class, array_column($storedEvents, 'event_class'));
});

it('should update a Role', function () {
    $role = \createRole();

    $roleId = $role->id;
    $initial_name = $role->name;

    UpdateRole::run($role, [
        'name' => 'SALES REP',
        'ability_names' => ['update.users', 'read.users', 'create.users', 'delete.users'],
        'group' => 5,
    ]);

    $this->assertEquals($roleId, $role->id);
    $this->assertNotEquals($initial_name, $role->name);
});

it('should have a update role event', function () {
    $role = \createRole();

    UpdateRole::run($role, [
        'name' => 'SALES REP',
        'ability_names' => ['update.users', 'read.users', 'create.users', 'delete.users'],
        'group' => 5,
    ]);

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(RoleUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should return 302 status for route roles endpoint for authenticated user', function () {
    $role = \createRole();
    $user = UserUtility::createUserWithTeam();

    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);
    $response = $this->get(route('roles'));

    $response->assertStatus(302);
});

it('should redirect an unauthenticated user to login for endpoint route roles', function () {
    $response = $this->get(route('roles'));

    $this->assertEquals(route('login'), $response->getTargetUrl());
});

it('should return a role for route role.export', function () {
    $role = \createRole();
    $client = Client::factory()->create();
    $user = UserUtility::createUserWithTeam();

    $user->client_id = $client->id;
    Bouncer::scope()->to($client->id);

    \createRole(['scope' => $user->client_id]);

    /** Admin */
    Bouncer::allow($role->name)->everything();

    $this->actingAs($user);

    $response = $this->get(route('roles.export'));

    $response->assertStatus(200);
});

it('should return 302 for route role.export', function () {
    $role = \createRole();
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);
    $response = $this->get(route('roles.export'));

    $response->assertStatus(302);
});

it('return 302 on route route.create', function () {
    $role = \createRole();
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);
    $response = $this->get(route('roles.create'));

    $response->assertStatus(302);
});

it('return 302 on route routes.edit', function () {
    $role = \createRole();
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);
    $role = Role::all()->first();
    $response = $this->get(route('roles.edit', ['role' => $role]));

    $response->assertStatus(302);
});

it('should return 302 on route routes.update', function () {
    //create a new role
    $role = \createRole();
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $name = fake()->jobTitle;
    $response = $this->put('roles/' . $role->id, [
        'name' => $name,
        'ability_names' => ['update.users', 'read.users', 'create.users', 'delete.users'],
        'group' => 6,
    ]);

    $role = Role::find($role->id);

    $this->assertEquals($role->name, $name);
    $response->assertStatus(302);
});

it('should return 302 on route routes.delete', function () {
    \createRole(['name' => 'Admin']);
    //create a new role
    $role = \createRole();
    $user = UserUtility::createUserWithTeam();

    /** Admin */
    Bouncer::allow('Admin')->everything();
    $this->actingAs($user);

    $initialCount = DB::table('roles')->count();

    $this->assertEquals($initialCount, 2);
    //The Following fails because the DeleteRole doesn't find the role when retrieving from the DB
    $response = $this->delete('roles/' . $role->id);

    $finalCount = DB::table('roles')->count();
    $this->assertEquals($finalCount, 1);

    $response->assertStatus(302);
});
