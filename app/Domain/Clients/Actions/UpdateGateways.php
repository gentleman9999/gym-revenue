<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\ClientSettingsAggregate;
use App\Domain\Clients\Models\Client;
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
            'client_id' => ['required', 'string', 'max:255', 'exists:clients,id'],
            'mailgunDomain' => ['sometimes', 'string', 'nullable'],
            'mailgunSecret' => ['sometimes', 'string', 'nullable'],
            'mailgunFromAddress' => ['sometimes', 'string', 'nullable'],
            'mailgunFromName' => ['sometimes', 'string', 'nullable'],
            'twilioSID' => ['sometimes', 'string', 'nullable'],
            'twilioToken' => ['sometimes', 'string', 'nullable'],
            'twilioNumber' => ['sometimes', 'string', 'nullable'],
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
