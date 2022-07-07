<?php

namespace App\Domain\Clients\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Clients\Models\Client;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteClient
{
    use AsAction;

    public string $commandSignature = 'client:delete {id}';
    public string $commandDescription = 'Permanently deletes the client';

    public function handle(string $id): Client
    {
        $client = Client::withTrashed()->findOrFail($id);
        ClientAggregate::retrieve($id)->delete()->persist();

        return $client;
    }

    public function asCommand(Command $command): void
    {
        $client = Client::withTrashed()->findOrFail($command->argument('id'));
        if ($command->confirm("Are you sure you want to hard delete client '{$client->name}'? This cannot be undone")) {
            $client = $this->handle($command->argument('id'));
            $command->info('Deleted client ' . $client->name);

            return;
        }
        $command->info('Aborted deleting client ' . $client->name);
    }
}
