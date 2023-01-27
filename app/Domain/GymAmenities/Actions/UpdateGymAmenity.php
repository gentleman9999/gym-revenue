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

class UpdateGymAmenity
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
            'name' => ['sometimes', 'required', 'string','max:50'],
        ];
    }

    public function handle(GymAmenity $gym_amenity, array $data): GymAmenity
    {
        GymAmenityAggregate::retrieve($gym_amenity->id)
            ->update($data)
            ->persist();

        return $gym_amenity->refresh();
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('gym-amenity.update', GymAmenity::class);
    }

    public function asController(ActionRequest $request, GymAmenity $gym_amenity): GymAmenity
    {
        $data = $request->validated();

        return $this->handle(
            $gym_amenity,
            $data
        );
    }

    public function htmlResponse(GymAmenity $gym_amenity): RedirectResponse
    {
        Alert::success("GymAmenity '{$gym_amenity->name}' was updated")->flash();

        return Redirect::back();
    }
}
