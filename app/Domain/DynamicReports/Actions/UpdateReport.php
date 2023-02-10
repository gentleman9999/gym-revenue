<?php

declare(strict_types=1);

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
    public function rules(): array
    {
        return [
            'name' => ['string', 'required'],
            'report' => ['array', 'sometimes'],
            'model' => ['required', 'string'],
            'filters' => ['nullable', 'string'],
        ];
    }

    public function handle(DynamicReport $dynamic_report, array $payload): DynamicReport
    {
        DynamicReportAggregate::retrieve($dynamic_report->id)->update($payload)->persist();

        return $dynamic_report->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('dynamic-reports.create', DynamicReport::class);
    }

    /**
     * @return string[]
     */
    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, DynamicReport $dynamic_report)
    {
        return $this->handle($dynamic_report, $request->validated());
    }

    public function htmlResponse(DynamicReport $dynamic_report): RedirectResponse
    {
        Alert::success("Report '{$dynamic_report->name}' was updated")->flash();

        return Redirect::route('dynamic-reports.edit', $dynamic_report->id);
    }
}
