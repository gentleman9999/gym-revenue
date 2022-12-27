<?php

declare(strict_types=1);

use App\Domain\Clients\Projections\Client;
use App\Domain\Teams\Actions\CreateTeam;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Actions\DeleteUser;
use App\Domain\Users\Actions\GrantAccessToken;
use App\Domain\Users\Actions\ImportUsers;
use App\Domain\Users\Actions\ObfuscateUser;
use App\Domain\Users\Actions\ResetUserPassword;
use App\Domain\Users\Actions\SetCustomUserCrudColumns;
use App\Domain\Users\Actions\SwitchTeam;
use App\Domain\Users\Actions\UpdateUser;
use App\Domain\Users\Events\UserCreated;
use App\Domain\Users\Events\UserDeleted;
use App\Domain\Users\Events\UserPasswordUpdated;
use App\Domain\Users\Events\UserSetCustomCrudColumns;
use App\Domain\Users\Events\UserUpdated;
use App\Domain\Users\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\Feature\Utilities\UserUtility;

beforeEach(function () {
    //
});

it('should have an update user event', function () {
    $first_name = fake()->firstName;
    $last_name = fake()->lastName;
    $user = UserUtility::createUserWithoutTeam();

    //run the update user action
    UpdateUser::run($user->id, [
        'first_name' => $first_name,
        'last_name' => $last_name,
    ]);

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(UserUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should have an delete user event', function () {
    //Given a new team and new user
    UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    DeleteUser::run($user);
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(UserDeleted::class, array_column($storedEvents, 'event_class'));
});

it('should have a user created event', function () {
    UserUtility::createRole(['name' => 'Admin']);
    UserUtility::createUserWithTeam();
    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(UserCreated::class, array_column($storedEvents, 'event_class'));
});

it('should allow the user to be deleted', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $this->delete("/users/{$user->id}", []);

    //There should not be any user models
    $this->assertEquals(User::count(), 0);
});

it('should not allow the user to be deleted', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    //record should not be deleted
    $this->delete('/users/' . rand(2, 20), []);

    //There should be one model
    $this->assertNotEquals(User::count(), 0);
});

it('should be unauthorized for the action to be attempted', function () {
    UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    $this->actingAs($user);

    //record should not be deleted
    $response = $this->delete('/users/' . $user->id, []);

    $response->assertStatus(403);
    //There should be one model
    $this->assertEquals(User::count(), 1);
});

it('should return a 404 on delete', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    //record should not be deleted
    $response = $this->delete('/users/' . rand(2, 20), []);

    $response->assertStatus(404);
});

it('should update the user data', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $first_name = fake()->firstName;
    $last_name = fake()->lastName;
    $user = UserUtility::createUserWithTeam();

    //run the update user action
    UpdateUser::run($user->id, [
        'first_name' => $first_name,
        'last_name' => $last_name,
    ]);

    $user->refresh();

    //user
    $this->assertEquals($user->first_name, $first_name);
    $this->assertEquals($user->last_name, $last_name);
});

it('should return 200 on route users/create', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $response = $this->get('/users/create', []);

    $this->assertEquals(200, $response->status());
});

it('should return 200 on route users/export', function () {
    UserUtility::createRole(['name' => 'Admin']);
    $client = Client::factory()->create();
    $user = UserUtility::createUserWithTeam();

    $user->client_id = $client->id;
    Bouncer::scope()->to($client->id);
    /** Admin **/
    Bouncer::allow('Admin')->everything();

    $this->actingAs($user);

    $response = $this->get('users/export', []);

    $this->assertEquals(200, $response->status());
});

it('should return 200 on route users', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $response = $this->get('users', []);

    $this->assertEquals(200, $response->status());
});

it('should return 200 on users/view/{user}', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    $viewable_user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $response = $this->get("users/view/{$viewable_user->id}", []);

    $this->assertEquals(200, $response->status());
});

it('should grant an access token on GrantAccessToken action', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();

    $this->assertEquals($user->access_token, null);

    Bouncer::allow($role->name)->everything();
    $user = GrantAccessToken::run($user);

    $this->assertIsString($user->access_token);
});

it('should reset user password on ResetPassword action', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    $old_password = $user->password;

    $this->assertIsString($old_password);
    Bouncer::allow($role->name)->everything();

    ResetUserPassword::run($user, [
        'password' => $user->password,
        'password_confirmation' => $user->password,
    ]);
//
    $user->refresh();

    $newPassHash = $user->password;
    $this->assertIsString($newPassHash);

    //the hashes should be different
    $this->assertNotEquals($newPassHash, $old_password);
});

it('should update the user password on route user-password.update', function () {
    $old_password = 'Hello123!';
    $new_password = fake()->unique()->password(8);

    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $this->put('user/password', [
        'current_password' => $old_password,
        'password' => $new_password,
        'password_confirmation' => $new_password,
    ]);

    $user->refresh();
    //user
    $this->assertTrue(Hash::check($new_password, $user->password));
});

it('should have a UserPasswordUpdated event on route user-password.update', function () {
    $old_password = "Hello123!";
    $new_password = fake()->unique()->password(8);

    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $this->put('user/password', [
        'current_password' => $old_password,
        'password' => $new_password,
        'password_confirmation' => $new_password,
    ]);

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(UserPasswordUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should return 200 on route impersonation/users', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    $team = Team::all()->first();

    UserUtility::createUser(['team_id' => $team->id], 3);

    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $response = $this->post('impersonation/users', ['team' => $team->id]);

    $response->assertstatus(200);
});

it('should return return array of users on route impersonation/users', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    $team = Team::all()->first();

    [$user1, $user2] = UserUtility::createUser(['team_id' => $team->id], 2);

    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $response = $this->post('impersonation/users', ['team' => $team->id]);
    $content_array = json_decode($response->getContent(), true);

    $this->assertEqualsCanonicalizing(array_column($content_array, 'name'), [$user1->name, $user2->name]);
});

it('should return 302 on route user/profile-information', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $response = $this->put('user/profile-information', []);

    $response->assertStatus(302);
});

it('should produce a UserUpdated event on on route user/profile-information', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $this->put('user/profile-information', [
        'email' => fake()->email,
    ]);

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(UserUpdated::class, array_column($storedEvents, 'event_class'));
});

it('should update the user email of their profile on route user/profile-information', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $new_email = fake()->email;
    $user->client_id = $user->team_id;

    Bouncer::allow('Admin')->everything();
    $this->actingAs($user);

    $this->put('user/profile-information', ['email' => $new_email]);
    $user->refresh();

    $this->assertEquals($new_email, $user->email);
});

it('should return 303 on delete user/profile-photo', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $file = __DIR__ . '/test.png';
    fopen($file, 'w');

    $user->forceFill(['profile_photo_path' => $file])->save();

    $response = $this->delete('user/profile-photo', []);
    unlink($file);

    $response->assertStatus(303);
});

it('should import users', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    $user->client_id = $user->team_id;

    ImportUsers::run([
        'bucket' => '',
        'extension' => 'csv',
        'expires' => '',
        'visibility' => '"public-read',
    ], $user->team_id);
})->skip();

it('should allow user to switch teams', function () {
    //create a new team
    $team = CreateTeam::run(Team::factory()->raw());

    SwitchTeam::run($team);

    $this->assertEquals(session('current_team')['name'], $team->name);
    $this->assertEquals(session('current_team')['id'], $team->id);
});

it('should have UserSetCustomCrudColumns event', function () {
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();
    Bouncer::allow($role->name)->everything();
    $this->actingAs($user);

    SetCustomUserCrudColumns::run([
        'table' => 'users',
        'columns' => ['first_name', 'last_name'],
    ], $user);

    $storedEvents = DB::table('stored_events')->get()->toArray();

    $this->assertContains(UserSetCustomCrudColumns::class, array_column($storedEvents, 'event_class'));
});

it('should Obfuscate User using ObfuscateUser action', function () {
    //Given a new team and new user
    UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();

    $this->assertEquals($user->obfuscated_at, null);
    $oUser = ObfuscateUser::run($user);
    $this->assertNotEquals($oUser->obfuscated_at, null);
});

it('should show Obfuscated global scope working', function () {
    //Given a new team and new user
    UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUserWithTeam();

    $originalCountOfUsers = User::all()->count();

    ObfuscateUser::run($user);
    $newCountOfUsers = User::all()->count();

    $this->assertEquals($originalCountOfUsers - 1, $newCountOfUsers);
});
