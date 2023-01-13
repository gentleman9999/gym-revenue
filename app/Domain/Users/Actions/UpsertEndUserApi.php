<?php

declare(strict_types=1);

namespace App\Domain\Users\Actions;

use App\Domain\Users\Aggregates\UserAggregate;
use App\Domain\Users\Models\EndUser;
use App\Enums\UserGenderEnum;
use App\Support\Uuid;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;
use Lorisleiva\Actions\ActionRequest;

abstract class UpsertEndUserApi extends BaseEndUserAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_type' => ['required', 'in:lead,customer,member'],
            'first_name' => ['sometimes', 'max:50'],
            'middle_name' => ['sometimes'],
            'last_name' => ['sometimes', 'max:30'],
            'email' => ['sometimes', 'email:rfc,dns'],
            'phone' => ['sometimes'],
            'alternate_phone' => ['sometimes'],
            'home_location_id' => ['sometimes', 'exists:locations,gymrevenue_id'],
            'client_id' => ['sometimes'],
            'profile_photo_path' => ['sometimes'],
            'gender' => ['sometimes', new Enum(UserGenderEnum::class)],
            'date_of_birth' => ['sometimes'],
            'opportunity' => ['sometimes'],
            'owner' => ['sometimes', 'exists:users,id'],
            'notes' => ['nullable', 'array'],
            'external_id' => ['sometimes', 'nullable'],
        ];
    }

    public function handle(array $data, $current_user = null)
    {
        $id = Uuid::new();

        if (array_key_exists('external_id', $data)) {
            $lead = EndUser::whereEmail($data['email'])
                ->orWhere('external_id', $data['external_id'])
                ->first();
        } else {
            $lead = EndUser::whereEmail($data['email'])
                ->first();
        }
        if (is_null($lead)) {
            $aggy = UserAggregate::retrieve((string)$id);
            $aggy->create($data);
        } else {
            $old_data = $lead->toArray();
            $aggy = UserAggregate::retrieve($old_data['id']);
            $aggy->update($data);
        }

        try {
            $aggy->persist();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return EndUser::findOrFail($id);
    }

    public function asController(ActionRequest $request): EndUser
    {
        return $this->handle(
            $request->validated(),
        );
    }
}
