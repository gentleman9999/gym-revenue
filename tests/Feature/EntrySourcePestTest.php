<?php

declare(strict_types=1);

use App\Domain\Clients\Projections\Client;
use App\Domain\EntrySources\Actions\CreateEntrySource;

beforeEach(function () {
    //
});

function createEntrySource(array $attributes = []): \App\Domain\EntrySources\EntrySource
{
    return CreateEntrySource::run(['name' => 'testing testing', 'client_id' => Client::factory()->create()->id,]);
}

it('create entry source using the CreateEntrySource action', function () {
    $data = ['name' => 'testing testing', 'client_id' => Client::factory()->create()->id,];
    $entry_source = \App\Domain\EntrySources\Actions\CreateEntrySource::run($data);

    $this->assertTrue($entry_source instanceof \App\Domain\EntrySources\EntrySource);
});

it('update entry source using the UpdateEntrySource action', function () {
    $entry_source = \createEntrySource();

    $data = [
        //'client_id' => Client::factory()->create()->id,
        'is_default_entry_source' => true,];
    $entry_source = \App\Domain\EntrySources\Actions\UpdateEntrySource::run($entry_source->id, $data);

    $this->assertTrue($entry_source instanceof \App\Domain\EntrySources\EntrySource);
});
