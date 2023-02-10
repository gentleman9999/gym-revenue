<?php

declare(strict_types=1);

namespace App\Domain\EntrySourceCategories\Actions;

use App\Domain\Clients\Projections\Client;
use App\Domain\EntrySourceCategories\EntrySourceCategory;
use App\Domain\EntrySourceCategories\EntrySourceCategoryAggregate;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateEntrySourceCategory
{
    use AsAction;

    public function rules(): array
    {
        return [
            'entrySourceCategories' => ['required', 'array'],
            'entrySourceCategories.*.name' => ['string', 'required'],
        ];
    }

    public function handle(array $data): EntrySourceCategory
    {
        EntrySourceCategoryAggregate::retrieve($data['id'])->update($data)->persist();

        return EntrySourceCategory::findOrFail($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('entry-source-category.update', EntrySourceCategory::class);
    }

    public function asController(ActionRequest $request): Client
    {
        $payload = [];
        foreach (json_decode($request->getContent())->entrySourceCategories as $key => $value) {
            $payload['id']    = $key;
            $payload['name']  = $value;
            $payload['value'] = str()->slug($value);
            $this->handle($payload);
        }

        return Client::findOrFail(request()->user()->client_id);
    }

    public function htmlResponse(Client $client): RedirectResponse
    {
        Alert::success("Client $client->name Entry Source Categories updated.")->flash();

        return (new RedirectResponse())->back();
    }
}
