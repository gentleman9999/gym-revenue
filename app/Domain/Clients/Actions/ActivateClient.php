<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Models\Client;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class ActivateClient
{
    use AsAction;

    public string $commandSignature = 'client:activate {id}';
    public string $commandDescription = 'Activates the client';

    public function handle(string $id): Client
    {
        return UpdateClient::run($id, ['active' => true]);
    }

    public function asCommand(Command $command): void
    {
        $client = $this->handle($command->argument('id'));
        $command->info('Activated Client ' . $client->name);
    }
}
