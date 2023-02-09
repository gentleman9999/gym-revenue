<?php

declare(strict_types=1);

namespace App\Domain\EntrySourceCategories\Actions;

use App\Domain\EntrySourceCategories\EntrySourceCategory;
use App\Domain\EntrySourceCategories\EntrySourceCategoryAggregate;
use App\Http\Middleware\InjectClientId;
use Illuminate\Console\View\Components\Alert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteEntrySourceCategory
{
    use AsAction;

    public function handle(EntrySourceCategory $esc, array $data): bool
    {
        EntrySourceCategoryAggregate::retrieve($esc->id)->delete($data)->persist();

        return true;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('entry-source-category.delete', EntrySourceCategory::class);
    }

    public function asController(string $esc_id): RedirectResponse
    {
        return $this->handle($esc_id);
    }

    public function htmlResponse(EntrySourceCategory $esc): RedirectResponse
    {
        Alert::success("Entry Source Category $esc->name deleted.")->flash();

        return Redirect::back();
    }
}
