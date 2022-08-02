<?php

namespace App\Domain\EndUsers\Actions;

use App\Http\Middleware\InjectClientId;
use Lorisleiva\Actions\ActionRequest;

abstract class BatchUpsertEndUserApi extends BaseEndUserAction
{
    abstract protected function getUpsertAction(): UpsertEndUserApi;

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
            '*.primary_phone' => ['sometimes'],
            '*.alternate_phone' => ['sometimes'],
            '*.gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            '*.client_id' => 'required',
            '*.profile_picture' => 'sometimes',
            '*.profile_picture.uuid' => 'sometimes|required',
            '*.profile_picture.key' => 'sometimes|required',
            '*.profile_picture.extension' => 'sometimes|required',
            '*.profile_picture.bucket' => 'sometimes|required',
            '*.gender' => 'sometimes|required',
            '*.date_of_birth' => 'sometimes|required',
            '*.opportunity' => 'sometimes|required',
            '*.owner' => 'sometimes|required|exists:users,id',
            '*.notes' => 'nullable|array',
            '*.external_id' => 'required',
        ];
    }

    public function handle(array $data): array
    {
        $response = [];

        foreach ($data as $item) {
            try {
                $response[] = ($this->getUpsertAction())::run($item);
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
