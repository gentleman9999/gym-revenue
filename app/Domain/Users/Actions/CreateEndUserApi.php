<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Models\EndUser;
use App\Support\Uuid;
use Lorisleiva\Actions\ActionRequest;

class CreateEndUserApi extends BaseEndUserAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_type' => ['required', 'in:lead,customer,member'],
            'first_name' => ['required', 'max:50'],
            'middle_name' => [],
            'last_name' => ['required', 'max:30'],
            'email' => ['required', 'email:rfc,dns'],
            'phone' => ['sometimes'],
            'alternate_phone' => ['sometimes'],
            'home_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            'client_id' => ['required'],
            'profile_photo_path' => ['sometimes'],
            'gender' => ['required'],
            'date_of_birth' => ['required'],
            'opportunity' => ['sometimes'],
            'owner' => ['sometimes', 'exists:users,id'],
            'notes' => ['nullable', 'array'],
        ];
    }

    public function handle(array $data): EndUser
    {
        $id = Uuid::new();
        UserAggregate::retrieve($id)->create($data)->persist();

        return EndUser::findOrFail($id);
    }

    public function asController(ActionRequest $request): EndUser
    {
        return $this->handle(
            $request->validated(),
        );
    }
}
