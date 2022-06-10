<?php

namespace App\Actions\Clients;

use App\Aggregates\Clients\ClientAggregate;
use App\Enums\ClientServiceEnum;
use App\Helpers\Uuid;
use App\Models\Clients\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateClient
{
    use AsAction;

    public string $commandSignature = 'client:create';
    public string $commandDescription = 'Creates a client with the given name.';

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string', 'required'],
            'active' => ['boolean', 'sometimes'],
            'services' => ['array', 'sometimes'],
        ];
    }

    public function handle(array $payload)
    {
        $id = Uuid::new();
        $payload['id'] = $id;

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

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('*');
    }

    public function asController(ActionRequest $request)
    {
        $client = $this->handle(
            $request->validated()
        );

        Alert::success("Client '{$client->name}' was created")->flash();

        return Redirect::back();
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
