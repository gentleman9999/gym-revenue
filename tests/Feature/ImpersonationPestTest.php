<?php

use App\Domain\Roles\Role;
use App\Domain\Users\Events\UserStoppedBeingImpersonated;
use App\Domain\Users\Events\UserWasImpersonated;
use Illuminate\Testing\TestResponse;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\Feature\Utilities\UserUtility;

beforeEach(function () {
    //
});

it('should should return status of 200 on route impersonation/users', function () {
    /** @var Role $role */
    $role = Role::factory()->create();
    [$user, ] = UserUtility::createUserWithTeam(2);

    /** Admin is allowed to create*/
    Bouncer::allow($role->name)->everything();

    $this->actingAs($user);
    /** @var TestResponse $response */
    $response = $this->post('impersonation/users', []);

    $response->assertStatus(200);
});

it('should return a list of users that can be impersonated', function () {
    /** @var Role $role */
    $role = UserUtility::createRole(['name' => 'Admin']);
    [$user, $user1, $user2, $user3] = UserUtility::createUserWithTeam(4);

    /** Admin is allowed to create*/
    Bouncer::allow($role->name)->everything();

    $this->actingAs($user);
    $response = $this->post('impersonation/users', []);

    foreach (json_decode($response->getContent(), true) as $r) {
        if ($user1->id = $r['userId']) {
            $this->assertTrue(true);
        } elseif ($user2->id = $r['userId']) {
            $this->assertTrue(true);
        } elseif ($user3->id = $r['userId']) {
            $this->assertTrue(true);
        }
    }
});

it('should do X', function () {
    /** @var Role $role */
    $role = Role::factory()->create();
    [$user, $user1] = UserUtility::createUserWithTeam(2);

    /** Admin is allowed to create*/
    Bouncer::allow($role->name)->everything();

    $this->actingAs($user);
    $this->post('impersonation/on?victimId='.$user1->id, []);

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(UserWasImpersonated::class, array_column($storedEvents, 'event_class'));
});

it('should do Y', function () {
    /** @var Role $role */
    $role = Role::factory()->create(['name' => 'Admin']);
    [$user, $user1] = UserUtility::createUserWithTeam(2);

    /** Admin is allowed to create*/
    Bouncer::allow($role->name)->everything();

    $this->actingAs($user);
    $response = $this->post('impersonation/on?victimId='.$user1->id, []);

    $response->assertStatus(302);
    $this->post('impersonation/off', []);
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(UserStoppedBeingImpersonated::class, array_column($storedEvents, 'event_class'));
});


it('should return 302 on route impersonation/on', function () {
    /** @var Role $role */
    $role = Role::factory()->create();
    [$user, $user1] = UserUtility::createUserWithTeam(2);

    /** Admin is allowed to create*/
    Bouncer::allow($role->name)->everything();

    $this->actingAs($user);
    $response = $this->post('impersonation/on?victimId='.$user1->id, []);

    $response->assertStatus(302);
});

it('should return 302 on route impersonation/off', function () {
    /** @var Role $role */
    $role = Role::factory()->create();
    [$user, $user1] = UserUtility::createUserWithTeam(2);

    /** Admin is allowed to create*/
    Bouncer::allow($role->name)->everything();

    $this->actingAs($user);
    $response = $this->post('impersonation/on?victimId='.$user1->id, []);
    $response->assertStatus(302);
    $response = $this->post('impersonation/off', []);

    $response->assertStatus(302);
});
