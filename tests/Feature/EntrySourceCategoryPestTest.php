<?php

declare(strict_types=1);

use App\Domain\Clients\Projections\Client;
use App\Domain\EntrySourceCategories\Actions\CreateEntrySourceCategory;
use App\Domain\EntrySourceCategories\Actions\DeleteEntrySourceCategory;
use App\Domain\EntrySourceCategories\Actions\RestoreEntrySourceCategory;
use App\Domain\EntrySourceCategories\Actions\TrashEntrySourceCategory;
use App\Domain\EntrySourceCategories\Actions\UpdateEntrySourceCategory;
use App\Domain\EntrySourceCategories\EntrySourceCategory;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\Feature\Utilities\UserUtility;

beforeEach(function () {
    //
});

function createEntrySourceCategory(array $attributes = []): EntrySourceCategory
{
    $client_id = Client::factory()->create()->id;
    $role = UserUtility::createRole(['name' => 'Admin']);
    $user = UserUtility::createUser(['client_id' => $client_id]);
    Bouncer::allow($role->name)->everything();

    return CreateEntrySourceCategory::run(['name' => 'testing testing', 'client_id' => $client_id], $user);
}

it('create entry source using the CreateEntrySourceCategory action', function () {
    $client_id = Client::factory()->create()->id;
    $data      = ['name' => 'testing testing', 'client_id' => $client_id];
    $role      = UserUtility::createRole(['name' => 'Admin']);
    $user      = UserUtility::createUser(['client_id' => $client_id]);
    Bouncer::allow($role->name)->everything();
    $esc = CreateEntrySourceCategory::run($data, $user);

    $this->assertTrue($esc instanceof EntrySourceCategory);
});

it('Trash entry source category using the TrashEntrySourceCategory action', function () {
    $esc  = createEntrySourceCategory();
    $data = [
        'id' => $esc->id,
    ];
    $esc  = TrashEntrySourceCategory::run($esc, $data);

    $this->assertTrue($esc instanceof EntrySourceCategory);
});

it('Restore entry source category using the RestoreEntrySourceCategory action', function () {
    $esc = createEntrySourceCategory();

    $data = [
        'id' => $esc->id,
    ];
    $esc  = RestoreEntrySourceCategory::run($esc, $data);

    $this->assertTrue($esc instanceof EntrySourceCategory);
});

it('Delete entry source category using the DeleteEntrySourceCategory action', function () {
    $esc = createEntrySourceCategory();

    $data = [
        'id' => $esc->id,
    ];
    $esc  = DeleteEntrySourceCategory::run($esc, $data);

    $this->assertTrue($esc == true);
});

it('update entry source category using the UpdateEntrySourceCategory action', function () {
    $esc = \createEntrySourceCategory();
    $esc = UpdateEntrySourceCategory::run([
        'id'   => $esc->id,
        'name' => 'Update Update',
    ]);

    $this->assertTrue($esc instanceof EntrySourceCategory);
});
