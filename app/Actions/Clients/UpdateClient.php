<?php

namespace App\Actions\Clients;

use App\Aggregates\Clients\ClientAggregate;
use App\Models\Clients\Client;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateClient
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string', 'sometimes'],
            'active' => ['boolean', 'sometimes'],
        ];
    }

    public function handle(string $id, array $payload)
    {
        ClientAggregate::retrieve($id)->update($payload)->persist();

        return Client::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('*');
    }

    public function asController(ActionRequest $request, Client $client)
    {
        $client = $this->handle(
            $client->id,
            $request->validated()
        );

        Alert::success("Client '{$client->name}' was updated")->flash();

        return Redirect::back();
    }
}
