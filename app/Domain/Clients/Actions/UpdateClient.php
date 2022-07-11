<?php

namespace App\Domain\Clients\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Clients\Models\Client;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateClient
{
    use AsAction;

    public function handle(string $id, array $payload): Client
    {
        ClientAggregate::retrieve($id)->update($payload)->persist();

        return Client::findOrFail($id);
    }
}
