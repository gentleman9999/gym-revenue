<?php

declare(strict_types=1);

namespace App\Domain\EntrySourceCategories\Actions;

use App\Domain\EntrySourceCategories\EntrySourceCategory;
use App\Domain\EntrySourceCategories\EntrySourceCategoryAggregate;
use App\Domain\Users\Models\User;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateEntrySourceCategory
{
    use AsAction;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'client_id' => ['required', 'string'],
        ];
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function handle(array $data, User $user): EntrySourceCategory
    {
        $esc_id = Uuid::get();

        EntrySourceCategoryAggregate::retrieve($esc_id)->create($data + [
            'client_id' => $user->client->id,
            'value' => str()->slug($data['name']),
        ])->persist();

        return EntrySourceCategory::findOrFail($esc_id);
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can('entry-source-category.create', EntrySourceCategory::class);
    }

    public function asController(ActionRequest $request): EntrySourceCategory
    {
        return $this->handle($request->validated(), $request->user());
    }

    public function htmlResponse(EntrySourceCategory $esc): RedirectResponse
    {
        Alert::success("Entry Source Category $esc->name created.")->flash();

        return (new RedirectResponse())->back();
    }
}
