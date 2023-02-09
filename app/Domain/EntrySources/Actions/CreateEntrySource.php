<?php

declare(strict_types=1);

namespace App\Domain\EntrySources\Actions;

use App\Domain\EntrySources\EntrySource;
use App\Domain\EntrySources\EntrySourceAggregate;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateEntrySource
{
    use AsAction;

    public function handle(array $data): EntrySource
    {
        $es_id = Uuid::get();
        EntrySourceAggregate::retrieve($es_id)->create($data)->persist();

        return EntrySource::findOrFail($es_id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }
}
