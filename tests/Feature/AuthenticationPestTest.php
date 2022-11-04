<?php

declare(strict_types=1);

use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Utilities\UserUtility;

beforeEach(function () {
    //
});

it('should be able to authenticate users using the login screen with the correct credentials', function () {
    UserUtility::createRole();
    $user = UserUtility::createUserWithTeam();
    Sanctum::actingAs($user, ['*']);

    //when user logs in
    /** @var TestResponse $response */
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'Hello123!',
    ]);

    $response->assertRedirect(route('dashboard'));
});

it('should not allow users with the wrong credentials to login', function () {
    UserUtility::createRole();
    $user = UserUtility::createUserWithTeam();
    Sanctum::actingAs($user, ['*']);

    //when user logs in
    /** @var TestResponse $response */
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong password',
    ]);

    //Expect an autheticated user to be able to reach the dashboard
    $this->assertNotEquals(route('dashboard'), $response->getTargetUrl());
});
