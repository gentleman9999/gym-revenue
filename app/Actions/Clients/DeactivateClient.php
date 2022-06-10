<?php

namespace App\Actions\Clients;

use App\Models\Clients\Client;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeactivateClient
{
    use AsAction;

    public string $commandSignature = 'client:deactivate {id}';
    public string $commandDescription = 'Deactivates the client';

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle($id, $current_user = null)
    {
        UpdateClient::run($id, ['active' => false], $current_user);

        return Client::withTrashed()->findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('*');
    }

    public function asController(Request $request, $id)
    {
        $client = $this->handle(
            $id,
            $request->user(),
        );

        Alert::success("Client '{$client->name}' was deactivated")->flash();

        return Redirect::back();
    }

    public function asCommand(Command $command): void
    {
        $client = $this->handle($command->argument('id'));
        $command->info('Deactivated Client ' . $client->name);
    }
}
