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

class RestoreGymAmenity
{
    use AsAction;

    public function handle(GymAmenity $gym_amenity): GymAmenity
    {
        GymAmenityAggregate::retrieve($gym_amenity->id)->restore()->persist();

        return $gym_amenity->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('gym-amenity.restore', GymAmenity::class);
    }

    public function asController(GymAmenity $gym_amenity): GymAmenity
    {
        return $this->handle($gym_amenity);
    }

    public function htmlResponse(GymAmenity $gym_amenity): RedirectResponse
    {
        Alert::success("GymAmenity '{$gym_amenity->name}' restored.")->flash();

        return Redirect::back();
    }
}
