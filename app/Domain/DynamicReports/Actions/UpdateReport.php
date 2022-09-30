<?php

namespace App\Domain\DynamicReports\Actions;

use App\Domain\DynamicReports\DynamicReportAggregate;
use App\Http\Middleware\InjectClientId;
use App\Models\DynamicReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateReport
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
            'name' => ['string', 'required'],
            'report' => ['array', 'sometimes'],
            'model' => ['required', 'string'],
            'filters' => ['nullable', 'string'],
        ];
    }

    public function handle(DynamicReport $dynamicReport, array $payload): DynamicReport
    {
        DynamicReportAggregate::retrieve($dynamicReport->id)->update($payload)->persist();

        return $dynamicReport->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('dynamic-reports.create', DynamicReport::class);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, DynamicReport $dynamicReport)
    {
        return $this->handle(
            $dynamicReport,
            $request->validated(),
        );
    }

    public function htmlResponse(DynamicReport $dynamicReport): RedirectResponse
    {
        Alert::success("Report '{$dynamicReport->name}' was updated")->flash();

        return Redirect::route('dynamic-reports.edit', $dynamicReport->id);
    }
}
