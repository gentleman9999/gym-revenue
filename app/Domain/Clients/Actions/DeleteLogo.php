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

class DeleteLogo
{
    use AsAction;

    public function handle($data)
    {
        ClientSettingsAggregate::retrieve($data['client_id'])
            ->deleteLogo($data)
            ->persist();

        return Client::findOrFail($data['client_id']);
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'string', 'max:255', 'exists:clients,id'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('manage-client-settings');
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(Client $client): RedirectResponse
    {
        Alert::success("Logo Deleted.")->flash();

        return Redirect::route('settings');
    }
}
