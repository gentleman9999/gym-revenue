<?php

declare(strict_types=1);

namespace App\Domain\EntrySources\Actions;

use App\Domain\Clients\Projections\Client;
use App\Domain\EntrySources\EntrySource;
use App\Domain\EntrySources\EntrySourceAggregate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateEntrySource
{
    use AsAction;

    public function rules(): array
    {
        return [
            'default_entry_source_id' => ['required', 'string'],
        ];
    }

    public function handle(string $es_id, array $data): EntrySource
    {
        EntrySourceAggregate::retrieve($es_id)->update($data)->persist();

        return EntrySource::findOrFail($es_id);
    }

    public function asController(ActionRequest $request): Client
    {
        $payload = [];
        $payload['is_default_entry_source'] = true;
        $this->handle($request->default_entry_source_id, $payload);

        return Client::findOrFail(request()->user()->client_id);
    }

    public function htmlResponse(Client $client): RedirectResponse
    {
        Alert::success("Client $client->name Entry Sources updated.")->flash();

        return Redirect::back();
    }
}
