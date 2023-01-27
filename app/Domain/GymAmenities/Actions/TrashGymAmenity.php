<?php

declare(strict_types=1);

namespace App\Domain\GymAmenities\Actions;

use App\Domain\GymAmenities\GymAmenityAggregate;
use App\Domain\GymAmenities\Projections\GymAmenity;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class TrashGymAmenity
{
    use AsAction;

    public function handle(string $gym_amenity_id): bool
    {
        GymAmenityAggregate::retrieve($gym_amenity_id)->trash()->persist();

        return true;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('gym-amenity.trash', GymAmenity::class);
    }

    public function asController(string $gym_amenity_id): bool
    {
        return $this->handle($gym_amenity_id);
    }

    public function htmlResponse(GymAmenity $gym_amenity): RedirectResponse
    {
        Alert::success("GymAmenity '{$gym_amenity->name}' sent to trash")->flash();

        return Redirect::back();
    }
}
