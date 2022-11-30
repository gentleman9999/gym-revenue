<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\ClientSettingsAggregate;
use App\Domain\Clients\Models\ClientGatewaySetting;
use App\Domain\Clients\Projections\Client;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateGateways
{
    use AsAction;

    public function handle(array $payload): Client
    {
        ClientSettingsAggregate::retrieve($payload['client_id'])->setGateways($payload)->persist();

        return Client::findOrFail($payload['client_id']);
    }

    public function rules(): array
    {
        return [
            ClientGatewaySetting::NAME_CLIENT_ID => ['required', 'string', 'max:255', 'exists:clients,id'],
            ClientGatewaySetting::NAME_MAILGUN_DOMAIN => ['sometimes', 'string', 'nullable'],
            ClientGatewaySetting::NAME_MAILGUN_SECRET => ['sometimes', 'string', 'nullable'],
            ClientGatewaySetting::NAME_MAILGUN_FROM_ADDRESS => ['sometimes', 'string', 'nullable'],
            ClientGatewaySetting::NAME_MAILGUN_FROM_NAME => ['sometimes', 'string', 'nullable'],
            ClientGatewaySetting::NAME_TWILIO_SID => ['sometimes', 'string', 'nullable'],
            ClientGatewaySetting::NAME_TWILIO_TOKEN => ['sometimes', 'string', 'nullable'],
            ClientGatewaySetting::NAME_TWILIO_NUMBER => ['sometimes', 'string', 'nullable'],
            ClientGatewaySetting::NAME_TWILIO_CONVERSATION_SERVICES_ID => ['sometimes', 'string', 'nullable'],
            ClientGatewaySetting::NAME_TWILIO_MESSENGER_ID => ['sometimes', 'string', 'nullable'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('manage-client-settings');
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request): Client
    {
        $data = $request->validated();

        return $this->handle(
            $data,
        );
    }

    public function htmlResponse(Client $client): RedirectResponse
    {
        Alert::success("Gateway '{$client->name}' services updated.")->flash();

        return Redirect::back();
    }
}
