<?php

declare(strict_types=1);

namespace App\Domain\Locations\Actions;

use App\Aggregates\Clients\ClientAggregate;
use App\Domain\Locations\Projections\Location;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class ImportLocations
{
    use AsAction;

    public function handle(array $data): bool
    {
        $result = false;
        foreach ($data as $item) {
            if ($item['extension'] === 'csv') {
                ClientAggregate::retrieve($item['client_id'])->importLocations($item['key'], $item['client_id'])->persist();
                $result = true;
            } else {
                Alert::error("File name: " . $item['filename'] . " doesn't meet extension requirements of '.csv'.")->flash();
            }
        }

        return $result;
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

        return $current_user->can('locations.create', Location::class);
    }

    public function asController(ActionRequest $request): bool
    {
        return $this->handle(
            $request->all()
        );
    }

    public function htmlResponse(bool $success): RedirectResponse
    {
        if ($success) { //If at-least one file was correct format and imported we display success
            Alert::success("Location Import complete.")->flash();
        } else {
            Alert::error("Location Import failed.")->flash();
        }

        return Redirect::back();
    }
}
