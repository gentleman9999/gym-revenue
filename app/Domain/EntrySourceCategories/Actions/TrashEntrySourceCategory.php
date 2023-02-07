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

class TrashEntrySourceCategory
{
    use AsAction;

    public function handle(EntrySourceCategory $esc, array $payload): EntrySourceCategory
    {
        EntrySourceCategoryAggregate::retrieve($esc->id)->trash($payload)->persist();

        return $esc->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('entry-source-category.trash', EntrySourceCategory::class);
    }

    public function asController(string $esc_id): Client
    {
        $payload = (new EntrySourceCategory())->whereId($esc_id);
        $this->handle(
            $payload,
            $payload->toArray(),
        );

        return Client::findOrFail(request()->user()->client_id);
    }

    public function htmlResponse(Client $client): RedirectResponse
    {
        Alert::success("Client $client->name Entry Source Category deleted.")->flash();

        return (new RedirectResponse())->back();
    }
}
