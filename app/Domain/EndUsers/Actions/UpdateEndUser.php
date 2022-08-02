<?php

namespace App\Domain\EndUsers\Actions;

use App\Domain\EndUsers\Projections\EndUser;

abstract class UpdateEndUser extends BaseEndUserAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['sometimes', 'max:50'],
            'middle_name' => ['sometimes'],
            'last_name' => ['sometimes', 'max:30'],
            'email' => ['sometimes', 'email:rfc,dns'],
            'primary_phone' => ['sometimes'],
            'alternate_phone' => ['sometimes'],
            'gr_location_id' => ['sometimes', 'exists:locations,gymrevenue_id'],
            'profile_picture' => 'sometimes',
            'profile_picture.uuid' => 'sometimes|required',
            'profile_picture.key' => 'sometimes|required',
            'profile_picture.extension' => 'sometimes|required',
            'profile_picture.bucket' => 'sometimes|required',
            'gender' => 'sometimes|required',
            'date_of_birth' => 'sometimes|required',
            'opportunity' => 'sometimes|required',
            'owner' => 'sometimes|required|exists:users,id',
            'notes' => 'nullable|array',
            'external_id' => ['sometimes', 'nullable'],
        ];
    }

    public function handle(EndUser $endUser, array $data)
    {
        ($this->getAggregate())::retrieve($endUser->id)->update($data)->persist();

        return $endUser->refresh();
    }
}
