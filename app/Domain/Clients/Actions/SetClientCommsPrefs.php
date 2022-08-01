<?php

namespace App\Domain\Clients\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Clients\Projections\Client;
use App\Http\Middleware\InjectClientId;
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
        ClientAggregate::retrieve($payload['client_id'])->setCommsPrefs($payload['commPreferences'])->persist();


        return Client::findOrFail($payload['client_id']);
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'string', 'max:255', 'exists:clients,id'],
            'commPreferences' => ['sometimes', 'array'],
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
        $data = $request->validated();

        return $this->handle(
            $data,
        );
    }

    public function htmlResponse(Client $client): RedirectResponse
    {
        Alert::success("Client '{$client->name}' comms updated.")->flash();

        return Redirect::back();
    }
}
