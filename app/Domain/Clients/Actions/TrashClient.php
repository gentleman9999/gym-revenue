<?php

namespace App\Domain\Clients\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Clients\Projections\Client;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class TrashClient
{
    use AsAction;

    public string $commandSignature = 'client:trash {id}';
    public string $commandDescription = 'Soft deletes the client';

    public function handle(string $id): Client
    {
        ClientAggregate::retrieve($id)->trash()->persist();

        return Client::withTrashed()->findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('*');
    }

    public function asCommand(Command $command): void
    {
        $client = $this->handle($command->argument('id'));
        $command->info('Soft Deleted client ' . $client->name);
    }
}
