<?php

use Laravel\Sanctum\Sanctum;
use Tests\Feature\Utilities\UserUtility;

beforeEach(function () {
    //
});

it('should allow profile information to be updated', function () {
    UserUtility::createRole();
    $user = UserUtility::createUserWithTeam();
    Sanctum::actingAs($user, ['*']);

    //login
    $this->post('/login', [
        'email' => $user->email,
        'password' => 'Hello123!',
    ]);

    //change user profile info
    $response = $this->put('/user/profile-information', [
        'id' => $user->id,
        'first_name' => $new_first_name = fake()->firstName,
        'last_name' => $new_last_name = fake()->lastName,
        'email' => $new_email = fake()->firstName,
    ]);

    $user->refresh();

    $this->assertEquals($response->status(), '302');
    $this->assertEquals("{$new_first_name} {$new_last_name}", $user->name);
    $this->assertEquals($new_email, $user->email);
});
