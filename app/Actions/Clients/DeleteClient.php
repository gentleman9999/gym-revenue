<?php

namespace App\Actions\Clients;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Client;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteClient
{
    use AsAction;

    public string $commandSignature = 'client:delete {id}';
    public string $commandDescription = 'Permanently deletes the client';

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

    public function handle(string $id, $current_user = null)
    {
        $client = Client::withTrashed()->findOrFail($id);
        ClientAggregate::retrieve($id)->delete($id, $current_user)->persist();

        return $client;
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

        Alert::success("Client '{$client->name}' was deleted")->flash();

        return Redirect::back();
    }

    public function asCommand(Command $command): void
    {
        $client = Client::withTrashed()->findOrFail($command->argument('id'));
        if ($command->confirm("Are you sure you want to hard delete client '{$client->name}'? This cannot be undone")) {
            $client = $this->handle($command->argument('id'));
            $command->info('Deleted client ' . $client->name);

            return;
        }
        $command->info('Aborted deleting client ' . $client->name);
    }
}
