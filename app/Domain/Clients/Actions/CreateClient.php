<?php

declare(strict_types=1);

namespace App\Domain\Clients\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Clients\Projections\Client;
use App\Enums\ClientServiceEnum;
use App\Support\Uuid;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

use function collect;

class CreateClient
{
    use AsAction;

    public string $commandSignature   = 'client:create';
    public string $commandDescription = 'Creates a client with the given name.';

    /**
     * @param array<string, mixed> $payload
     *
     */
    public function handle(array $payload): Client
    {
        $id = Uuid::get();

        $aggy = ClientAggregate::retrieve($id);

        $aggy->create($payload)->persist();

        return Client::findOrFail($id);
    }

    public function asCommand(Command $command): void
    {
        $client_name               = $command->ask("Enter Client Name");
        $activated                 = $command->confirm("Activate?");
        $client_feature_enums      = collect(ClientServiceEnum::cases())->pluck('name');
        $available_client_services = $client_feature_enums->prepend("ALL")->toArray();
        $client_services           = $command->choice("Which client services to enable?", $available_client_services, 0, null, true);
        if ($client_services[0] === "ALL") {
            $client_services = $available_client_services;
        }

        $client_data = [
            'name' => $client_name,
            'active' => $activated,
        ];

        if ($client_services !== null && count($client_services)) {
            $client_data['services'] = $client_services;
        }

        $client = $this->handle(
            $client_data
        );

        $command->info('Created client ' . $client->name);
    }
}
