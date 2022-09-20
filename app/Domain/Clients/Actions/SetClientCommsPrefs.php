<?php

namespace App\Domain\Clients\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Clients\Projections\Client;
use App\Http\Middleware\InjectClientId;
use App\Models\ClientCommunicationPreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class SetClientCommsPrefs
{
    use AsAction;

    public function handle(array $payload): Client
    {
//        dd($payload);
//        $this->services ?? [];
//        ClientAggregate::retrieve($payload['client_id'])->setClientServices($payload['services'])->persist();
        ClientAggregate::retrieve($payload['client_id'])->setCommsPrefs($payload)->persist();


        return Client::findOrFail($payload['client_id']);
    }

    /**
     * Prepare the data for validation.
     *
     * @param ActionRequest $request
     *
     * @return void
     */
    public function prepareForValidation(ActionRequest $request): void
    {
        $request->mergeIfMissing([
            ClientCommunicationPreference::COMMUNICATION_TYPES_EMAIL => false,
            ClientCommunicationPreference::COMMUNICATION_TYPES_SMS => false,
            ClientCommunicationPreference::COMMUNICATION_TYPES_VOICE => false,
            ClientCommunicationPreference::COMMUNICATION_TYPES_CONVERSATION => false,
        ]);
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'string', 'max:255', 'exists:clients,id'],
            ClientCommunicationPreference::COMMUNICATION_TYPES_EMAIL => ['required', 'bool'],
            ClientCommunicationPreference::COMMUNICATION_TYPES_SMS => ['required', 'bool'],
            ClientCommunicationPreference::COMMUNICATION_TYPES_VOICE => ['required', 'bool'],
            ClientCommunicationPreference::COMMUNICATION_TYPES_CONVERSATION => ['required', 'bool'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
//        $this->services = $request->services ?? [];

        return $current_user->can('manage-client-settings');
    }

    //services array is available in request
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request): Client
    {
        return $this->handle($request->validated());
    }

    public function htmlResponse(Client $client): RedirectResponse
    {
        Alert::success("Client '{$client->name}' comms updated.")->flash();

        return Redirect::back();
    }
}
