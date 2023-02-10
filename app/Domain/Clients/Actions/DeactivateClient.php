<?php

declare(strict_types=1);

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Projections\Client;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class DeactivateClient
{
    use AsAction;

    public string $commandSignature   = 'client:deactivate {id}';
    public string $commandDescription = 'Deactivates the client';

    public function handle(string $id): Client
    {
        return UpdateClient::run($id, ['active' => false]);
    }

    public function asCommand(Command $command): void
    {
        $client = $this->handle($command->argument('id'));
        $command->info('Deactivated Client ' . $client->name);
    }
}
