<?php

declare(strict_types=1);

namespace App\Domain\Clients\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Clients\Projections\Client;
use Illuminate\Console\Command;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class RestoreClient
{
    use AsAction;

    public string $commandSignature   = 'client:restore {id}';
    public string $commandDescription = 'Restores the client';

    public function handle(string $id): Client
    {
        ClientAggregate::retrieve($id)->restore()->persist();

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
        $command->info('Restored Client ' . $client->name);
    }
}
