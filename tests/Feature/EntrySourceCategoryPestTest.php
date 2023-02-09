<?php

declare(strict_types=1);

use App\Domain\Clients\Projections\Client;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\Feature\Utilities\UserUtility;

beforeEach(function () {
    //
});

function createEntrySourceCategory(array $attributes = []): \App\Domain\EntrySourceCategories\EntrySourceCategory
{
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = \Tests\Feature\Utilities\UserUtility::createUser(['client_id' => Client::factory()->create()->id]);
    Bouncer::allow($role->name)->everything();

    return \App\Domain\EntrySourceCategories\Actions\CreateEntrySourceCategory::run(['name' => 'testing testing'], $user);
}

it('create entry source using the CreateEntrySourceCategory action', function () {
    $data = ['name' => 'testing testing', ];
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = \Tests\Feature\Utilities\UserUtility::createUser(['client_id' => Client::factory()->create()->id]);
    Bouncer::allow($role->name)->everything();
    $esc = \App\Domain\EntrySourceCategories\Actions\CreateEntrySourceCategory::run($data, $user);

    $this->assertTrue($esc instanceof \App\Domain\EntrySourceCategories\EntrySourceCategory);
});

it('Trash entry source category using the TrashEntrySourceCategory action', function () {
    $esc = \createEntrySourceCategory();
    $data = [
        'id' => $esc->id,];
    $esc = \App\Domain\EntrySourceCategories\Actions\TrashEntrySourceCategory::run($esc, $data);

    $this->assertTrue($esc instanceof \App\Domain\EntrySourceCategories\EntrySourceCategory);
});

it('Restore entry source category using the RestoreEntrySourceCategory action', function () {
    $esc = \createEntrySourceCategory();

    $data = [
        'id' => $esc->id,
        ];
    $esc = \App\Domain\EntrySourceCategories\Actions\RestoreEntrySourceCategory::run($esc, $data);

    $this->assertTrue($esc instanceof \App\Domain\EntrySourceCategories\EntrySourceCategory);
});

it('Delete entry source category using the DeleteEntrySourceCategory action', function () {
    $esc = \createEntrySourceCategory();

    $data = [
        'id' => $esc->id,
        ];
    $esc = \App\Domain\EntrySourceCategories\Actions\DeleteEntrySourceCategory::run($esc, $data);

    $this->assertTrue($esc == true);
});

it('update entry source category using the UpdateEntrySourceCategory action', function () {
    $esc = \createEntrySourceCategory();

    $data = [
        'id' => $esc->id,
        'name' => 'Update Update',];
    $esc = \App\Domain\EntrySourceCategories\Actions\UpdateEntrySourceCategory::run($data);

    $this->assertTrue($esc instanceof \App\Domain\EntrySourceCategories\EntrySourceCategory);
});
