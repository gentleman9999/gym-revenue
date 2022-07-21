<?php

namespace App\Domain\Clients\Actions;

use App\Domain\Clients\ClientSettingsAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UploadLogo
{
    use AsAction;

    public function handle($data, $current_user): bool
    {
        $result = false;
        foreach ($data as $item) {
            $item['client_id'] = $current_user->client_id;
            ClientSettingsAggregate::retrieve($item['client_id'])->uploadLogo($item)->persist();
            $result = true;
        }

        return $result;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('manage-client-settings');
    }

    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'nullable','string', 'max:255', 'exists:clients,id'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request): bool
    {
        return $this->handle(
            $request->all(),
            $request->user(),
        );
    }

    public function htmlResponse(bool $success): RedirectResponse
    {
        if ($success) { //If at-least one file was correct format and imported we display success
            Alert::success("Logo Import complete.")->flash();
        }

        return Redirect::route('settings');
    }
}
