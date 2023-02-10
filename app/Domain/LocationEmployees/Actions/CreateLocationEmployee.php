<?php

declare(strict_types=1);

namespace App\Domain\LocationEmployees\Actions;

use App\Domain\LocationEmployees\LocationEmployeeAggregate;
use App\Domain\LocationEmployees\Projections\LocationEmployee;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class CreateLocationEmployee
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
            'client_id' => ['required', 'exists:clients,id'],
            'location_id' => ['sometimes','exists:locations,id'],
            'department_id' => ['required', 'exists:locations,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'user_id' => ['required', 'exists:users,id'],
            'primary_supervisor_user_id' => ['sometimes'],
        ];
    }

    /**
     * @param array<string, mixed> $data
     *
     */
    public function handle(array $data): LocationEmployee
    {
        $id = (string) Uuid::new();//we should use uuid here
        LocationEmployeeAggregate::retrieve($id)->create($data)->persist();

        return LocationEmployee::findOrFail($id);
    }

    public function asController(ActionRequest $request): LocationEmployee
    {
        return $this->handle(
            $request->validated()
        );
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

        return $current_user->can('locationEmployee.create', LocationEmployee::class);
    }

    public function htmlResponse(LocationEmployee $location_employee): RedirectResponse
    {
        Alert::success("Location for this employee was created")->flash();

        return Redirect::back();
    }
}
