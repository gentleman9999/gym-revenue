<?php

declare(strict_types=1);

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\Events\ClientGatewayIntegrationCreated;
use App\Domain\Clients\Projections\Client;
use App\Models\GatewayProviders\GatewayProvider;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateClientGatewayIntegration
{
    use AsAction;

    public string $commandSignature = 'client:create-gateway';
    public string $commandDescription = 'Creates a gateway integration for client.';

    public function handle(Client $client, GatewayProvider $gateway_provider, string $nickname): void
    {
        event(new ClientGatewayIntegrationCreated([
            'client_id' => $client->id,
            'gateway_id' => $gateway_provider->id,
            'nickname' => $nickname,
        ]));
    }

    public function asCommand(Command $command): void
    {
        $client_names = $command->choice(
            'Select Client (comma separated multiple options)',
            Client::all()->pluck('name')->toArray(),
            multiple: true,
        );

        $gateway_provider_name = $command->choice(
            'Select Gateway Provider (comma separated multiple options)',
            GatewayProvider::all()->pluck('name')->toArray(),
            multiple: true,
        );


        $clients = Client::whereIn('name', $client_names)->get();
        $gateways = GatewayProvider::whereIn('name', $gateway_provider_name)->get();
        $message = '';

        foreach ($clients as $client) {
            foreach ($gateways as $gateway) {
                $name = "{$client->name}({$gateway->name})";
                $integration_name = $command->ask("Enter gateway integration nickname for {$name}");
                $this->handle($client, $gateway, $integration_name ?? $gateway->name);

                $message .= "{$name} integration created.\n";
            }
        }

        $command->info($message);
    }
}
