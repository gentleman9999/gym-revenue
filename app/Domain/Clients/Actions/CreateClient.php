<?php

namespace App\Domain\Clients\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Clients\Models\Client;
use App\Enums\ClientServiceEnum;
use App\Helpers\Uuid;
use function collect;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateClient
{
    use AsAction;

    public string $commandSignature = 'client:create';
    public string $commandDescription = 'Creates a client with the given name.';

    public function handle(array $payload): Client
    {
        $id = Uuid::new();

        $aggy = ClientAggregate::retrieve($id);

        $aggy->create($payload)
            ->createAudience("{$payload['name']} Prospects", 'prospects', /*env('MAIL_FROM_ADDRESS'),*/ 'auto')
            ->createAudience("{$payload['name']} Conversions", 'conversions', /*env('MAIL_FROM_ADDRESS'),*/ 'auto');
//            ->createGatewayIntegration('sms', 'twilio', 'default_cnb', 'auto')
//            ->createGatewayIntegration('email', 'mailgun', 'default_cnb', 'auto')
        $aggy->persist();

        $client_id = $payload['client_id'] ?? null;

        if ($client_id) {
            ClientAggregate::retrieve($payload['client_id'])->attachTeamToClient($id)->persist();
        }

        return Client::findOrFail($id);
    }

    public function asCommand(Command $command): void
    {
        $client_name = $command->ask("Enter Client Name");
        $activated = $command->confirm("Activate?");
        $client_feature_enums = collect(ClientServiceEnum::cases())->pluck('name');
        $available_client_services = $client_feature_enums->prepend("ALL")->toArray();
        $client_services = $command->choice("Which client services to enable?", $available_client_services, 0, null, true);
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
