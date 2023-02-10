<?php

declare(strict_types=1);

namespace App\Domain\DynamicReports\Actions;

use App\Domain\DynamicReports\DynamicReportAggregate;
use App\Http\Middleware\InjectClientId;
use App\Models\DynamicReport;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateReport
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
            'name' => ['required', 'string','min:2'],
            'client_id' => ['string', 'required'],
            'model' => ['required', 'string'],
            'filters' => ['nullable', 'string'],
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): DynamicReport
    {
        $id = Uuid::get();

        $aggy = DynamicReportAggregate::retrieve($id);
        $aggy->create($data)->persist();

        return DynamicReport::findOrFail($id);
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

        return $current_user->can('dynamic-reports.create', DynamicReport::class);
    }

    public function asController(ActionRequest $request): DynamicReport
    {
        return $this->handle(
            $request->validated()
        );
    }

    public function htmlResponse(DynamicReport $dr): RedirectResponse
    {
        Alert::success("Dynamic Report '{$dr->name}' was created")->flash();

        return Redirect::route('dynamic-reports.edit', $dr->id);
    }
}
