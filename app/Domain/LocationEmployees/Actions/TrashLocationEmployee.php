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

class TrashLocationEmployee
{
    use AsAction;

    public function handle(string $location_employee_id): bool
    {
        LocationEmployeeAggregate::retrieve($location_employee_id)->trash()->persist();

        return true;
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

        return $current_user->can('LocationEmployee.trash', LocationEmployee::class);
    }

    public function asController(string $location_employee_id): bool
    {
        return $this->handle($location_employee_id);
    }

    public function htmlResponse(LocationEmployee $location_employee): RedirectResponse
    {
        Alert::success("LocationEmployee sent to trash")->flash();

        return Redirect::back();
    }
}
