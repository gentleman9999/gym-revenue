<?php

declare(strict_types=1);

namespace App\Domain\GymAmenities\Actions;

use App\Domain\GymAmenities\GymAmenityAggregate;
use App\Domain\GymAmenities\Projections\GymAmenity;
use App\Http\Middleware\InjectClientId;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateGymAmenity
{
    use AsAction;

    public function rules(): array
    {
        return [
            'client_id' => ['required','string'],
            'location_id' => ['required', 'exists:locations,gymrevenue_id'],
            'name' => ['string','max:50'],
            'capacity' => ['integer'],
            'working_hour' => ['integer'],
            'started_at' => ['sometimes'],
            'closed_at' => ['sometimes'],
        ];
    }

    public function handle(array $data): GymAmenity
    {
        $id = Uuid::get();//we should use uuid here
        GymAmenityAggregate::retrieve($id)->create($data)->persist();

        return GymAmenity::findOrFail($id);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('gym-amenity.create', GymAmenity::class);
    }

    public function asController(ActionRequest $request): RedirectResponse
    {
        return $this->handle(
            $request->validated()
        );
    }
}
