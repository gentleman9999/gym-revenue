<?php

declare(strict_types=1);

namespace App\Domain\EntrySourceCategories\Actions;

use App\Domain\EntrySourceCategories\EntrySourceCategory;
use App\Domain\EntrySourceCategories\EntrySourceCategoryAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class RestoreEntrySourceCategory
{
    use AsAction;

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('entry-source-category.restore', EntrySourceCategory::class);
    }

    public function handle(EntrySourceCategory $esc, array $data): EntrySourceCategory
    {
        EntrySourceCategoryAggregate::retrieve($esc->id)->restore($data)->persist();

        return $esc->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(EntrySourceCategory $esc): EntrySourceCategory
    {
        return $this->handle(
            $esc,
        );
    }

    public function htmlResponse(EntrySourceCategory $esc): RedirectResponse
    {
        Alert::success("Entry Source Category $esc->name restored.")->flash();

        return Redirect::back();
    }
}
