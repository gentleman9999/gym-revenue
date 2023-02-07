<?php

declare(strict_types=1);

namespace App\Domain\EntrySources\Actions;

use App\Domain\EntrySources\EntrySource;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateEntrySources
{
    use AsAction;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'sources' => ['array', 'required', 'min:1'],
            'sources.*.id' => ['string', 'nullable'],
            'sources.*.name' => ['string', 'required', 'max:50'],
            'client_id' => ['string', 'nullable', 'max:50'],
        ];
    }

    public function handle(array $data): array
    {
        $sources = $data['sources'];
        $sourcesToUpdate = collect($sources)->filter(function ($s) {
            return $s['id'] !== null && ! empty($s['name']);
        });
        $sourcesToCreate = collect($sources)->filter(function ($s) {
            return $s['id'] === null && ! empty($s['name']);
        });

        $changed_sources = [];

        foreach ($sourcesToUpdate as $sourceToUpdate) {
            $changed_sources[] = UpdateEntrySource::run($sourceToUpdate['id'], [
                'source' => $sourceToUpdate['name'],
                'name' => $sourceToUpdate['name'],
            ]);
        }
        foreach ($sourcesToCreate as $sourceToCreate) {
            $changed_sources[] = CreateEntrySource::run([
                'source' => $sourceToCreate['name'],
                'name' => $sourceToCreate['name'],
                'client_id' => $data['client_id'],
            ]);
        }

        return $changed_sources;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('lead-sources.create', EntrySource::class);
    }

    public function asController(ActionRequest $request): array
    {
        return $this->handle(
            $request->validated(),
        );
    }

    public function htmlResponse(array $entry_sources): RedirectResponse
    {
        $entry_source_count = count($entry_sources);
        Alert::success("$entry_source_count Entry Sources updated.")->flash();

        return Redirect::back();
    }
}
