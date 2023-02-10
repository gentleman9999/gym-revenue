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

class RestoreLocationEmployee
{
    use AsAction;

    public function handle(LocationEmployee $location_employee): LocationEmployee
    {
        LocationEmployeeAggregate::retrieve($location_employee->id)->restore()->persist();

        return $location_employee->refresh();
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

        return $current_user->can('locationEmployee.restore', LocationEmployee::class);
    }

    public function asController(LocationEmployee $location_employee): LocationEmployee
    {
        return $this->handle($location_employee);
    }

    public function htmlResponse(LocationEmployee $location_employee): RedirectResponse
    {
        Alert::success("LocationEmployee restored.")->flash();

        return Redirect::back();
    }
}
