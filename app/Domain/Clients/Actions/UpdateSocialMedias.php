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

class UpdateSocialMedias
{
    use AsAction;

    public function handle(array $payload): Client
    {
        ClientSettingsAggregate::retrieve($payload['client_id'])->setSocialMedias($payload['socialMedias'])->persist();

        return Client::findOrFail($payload['client_id']);
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'string', 'max:255', 'exists:clients,id'],
            'socialMedias' => [ 'sometimes', 'array', 'nullable'],
            'socialMedias.*' => [ 'string'],
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
        Alert::success("Social Media '{$client->name}' services updated.")->flash();

        return Redirect::back();
    }
}
