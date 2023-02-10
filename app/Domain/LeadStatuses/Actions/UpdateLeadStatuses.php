<?php

declare(strict_types=1);

namespace App\Domain\LeadStatuses\Actions;

use App\Domain\LeadStatuses\LeadStatus;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateLeadStatuses
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
            'statuses' => ['array', 'required', 'min:1'],
            'statuses.*.id' => ['string', 'nullable'],
            'statuses.*.status' => ['string', 'required', 'max:50'],
            'client_id' => ['string', 'nullable', 'max:50'],
        ];
    }

    public function handle(array $data): array
    {
        $statuses         = $data['statuses'];
        $statusesToUpdate = collect($statuses)->filter(function ($s) {
            return $s['id'] !== null && ! empty($s['status']);
        });
        $statusesToCreate = collect($statuses)->filter(function ($s) {
            return $s['id'] === null && ! empty($s['status']);
        });

        $changed_statuses = [];

        foreach ($statusesToUpdate as $statusToUpdate) {
            $changed_statuses[] = UpdateLeadStatus::run($statusToUpdate['id'], ['status' => $statusToUpdate['status']]);
        }

        foreach ($statusesToCreate as $statusToCreate) {
            $changed_statuses[] = CreateLeadStatus::run([
                'status' => $statusToCreate['status'],
                'client_id' => $data['client_id'],
            ]);
        }

        return $changed_statuses;
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('lead-statuses.create', LeadStatus::class);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->validated(),
        );
    }

    public function htmlResponse(array $leadStatuses): RedirectResponse
    {
        $leadStatusesCount = count($leadStatuses);
        Alert::success("{$leadStatusesCount} Lead Statuses updated.")->flash();

        return Redirect::back();
    }
}
