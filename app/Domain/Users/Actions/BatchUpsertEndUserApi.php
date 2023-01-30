<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Enums\UserGenderEnum;
use App\Http\Middleware\InjectClientId;
use Illuminate\Validation\Rules\Enum;
use Lorisleiva\Actions\ActionRequest;

class BatchUpsertEndUserApi extends BaseEndUserAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            '*.first_name' => ['required', 'max:50'],
            '*.middle_name' => [],
            '*.last_name' => ['required', 'max:30'],
            '*.email' => ['required', 'email:rfc,dns'],
            '*.phone' => ['sometimes'],
            '*.alternate_phone' => ['sometimes'],
            '*.home_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            '*.client_id' => ['required'],
            '*.profile_photo_path' => ['sometimes'],
            '*.gender' => ['sometimes', 'required', new Enum(UserGenderEnum::class)],
            '*.date_of_birth' => ['sometimes'],
            '*.opportunity' => ['sometimes'],
            '*.owner' => ['sometimes', 'exists:users,id'],
            '*.notes' => ['nullable', 'array'],
            '*.external_id' => ['required'],
        ];
    }

    public function handle(array $data): array
    {
        $response = [];

        foreach ($data as $item) {
            try {
                $response[] = UpsertEndUserApi::run($item);
            } catch (\Exception $e) {
                $response[] = $e;
            }
        }

        return $response;
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request): array
    {
        return $this->handle(
            $request->validated(),
        );
    }
}
