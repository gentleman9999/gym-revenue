<?php

declare(strict_types=1);

namespace App\Domain\LocationEmployees\Actions;

use App\Domain\LocationEmployees\LocationEmployeeAggregate;
use App\Domain\LocationEmployees\Projections\LocationEmployee;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateLocationEmployee
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
            'client_id' => ['sometimes', 'exists:clients,id'],
            'location_id' => ['sometimes', 'exists:locations,id'],
            'department_id' => ['sometimes', 'exists:locations,id'],
            'position_id' => ['sometimes', 'exists:positions,id'],
            'user_id' => ['sometimes', 'exists:users,id'],
            'primary_supervisor_user_id' => ['sometimes'],
        ];
    }

    public function handle(LocationEmployee $location_employee, array $data): LocationEmployee
    {
        LocationEmployeeAggregate::retrieve($location_employee->id)
            ->update($data)
            ->persist();

        return $location_employee->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('LocationEmployee.update', LocationEmployee::class);
    }

    public function asController(ActionRequest $request, LocationEmployee $location_employee): LocationEmployee
    {
        $data = $request->validated();

        return $this->handle(
            $location_employee,
            $data
        );
    }

    public function htmlResponse(LocationEmployee $location_employee): RedirectResponse
    {
        Alert::success("LocationEmployee was updated")->flash();

        return Redirect::back();
    }
}
