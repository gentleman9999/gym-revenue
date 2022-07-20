<?php

namespace App\Domain\LeadSources\Actions;

use App\Domain\LeadSources\LeadSource;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateLeadSources
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
            'sources' => ['array', 'required', 'min:1'],
            'sources.*.id' => ['string', 'nullable'],
            'sources.*.name' => ['string', 'required', 'max:50'],
            'client_id' => ['string', 'nullable', 'max:50'],
        ];
    }

    public function handle(array $data)
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
            $changed_sources[] = UpdateLeadSource::run($sourceToUpdate['id'], [
                'source' => $sourceToUpdate['name'],
                'name' => $sourceToUpdate['name'],
            ]);
        }
        foreach ($sourcesToCreate as $sourceToCreate) {
            $changed_sources[] = CreateLeadSource::run([
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

        return $current_user->can('lead-sources.create', LeadSource::class);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->validated(),
        );
    }

    public function htmlResponse(array $leadSources): RedirectResponse
    {
        $leadSourcesCount = count($leadSources);
        Alert::success("{$leadSourcesCount} Lead Sources updated.")->flash();

        return Redirect::back();
    }
}