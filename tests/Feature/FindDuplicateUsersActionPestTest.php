<?php

declare(strict_types=1);

use App\Domain\Users\Actions\CreateUser;
use App\Domain\Users\Actions\FindDuplicateUsers;
use App\Domain\Users\Models\User;
use Database\Seeders\Data\NicknameSeeder;

beforeEach(function () {
    CreateUser::run(User::factory()->raw([
        'first_name' => 'Alexander',
        'last_name' => 'Isak',
        'email' => 'alex.isak@gmail.com',
        'phone' => '4199081945',
        'zip' => '15859',
    ]));

    CreateUser::run(User::factory()->raw([
        'first_name' => 'Alexander',
        'last_name' => 'Isak',
        'email' => 'alex.isak+second@gmail.com',
        'phone' => '4199081945',
        'zip' => '15859',
    ]));

    CreateUser::run(User::factory()->raw([
        'first_name' => 'Karli',
        'last_name' => 'Hoeger',
        'email' => 'karli.hoeger@gmail.com',
        'phone' => '4199082045',
        'zip' => '15860',
    ]));
});

it('should return 2 matches', function () {
    $data = [
        'first_name' => 'Alexander',
        'last_name' => 'Isak',
        'email' => 'alex.isak@gmail.com',
        'phone' => '4199081945',
        'date_of_birth' => '1980-11-16',
        'zip' => '15859',
    ];

    $duplicateUsers = FindDuplicateUsers::run($data);

    $this->assertEquals($duplicateUsers->count(), 2);

    $data['email'] = 'alex.isak+777@gmail.com';

    $duplicateUsers = FindDuplicateUsers::run($data);

    $this->assertEquals($duplicateUsers->count(), 2);
});

it('should return 1 matches with 1 score', function () {
    $data = [
        'first_name' => 'Karim',
        'last_name' => 'Benzema',
        'email' => 'karli.hoeger@gmail.com',
        'phone' => '4200082040',
        'zip' => '16870',
    ];

    $duplicateUsers = FindDuplicateUsers::run($data);

    $this->assertEquals($duplicateUsers->count(), 1);
    $this->assertEquals($duplicateUsers->first()->score, 1);
});

it('should match nickname and return 2 users with 0.33 score', function () {
    $this->seed(NicknameSeeder::class);

    $data = [
        'first_name' => 'Sandra',
        'last_name' => 'Isak',
        'email' => 'sandra.isak@gmail.com',
        'phone' => '4100901845',
        'zip' => '15880',
    ];

    $duplicateUsers = FindDuplicateUsers::run($data);

    $this->assertEquals($duplicateUsers->count(), 2);
    $this->assertEquals($duplicateUsers->first()->score, 0.33);
});

it('should return 0 matches', function () {
    $data = [
        'first_name' => 'Kyle',
        'last_name' => 'Walker',
        'email' => 'kyle.waler@gmail.com',
        'phone' => '4100081845',
        'zip' => '15809',
    ];

    $duplicateUsers = FindDuplicateUsers::run($data);

    $this->assertEquals($duplicateUsers->count(), 0);
});
