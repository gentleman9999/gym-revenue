<?php

namespace App\Domain\EndUsers\Actions;

use App\Support\Uuid;
use Lorisleiva\Actions\ActionRequest;

class CreateEndUserApi extends BaseEndUserAction
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'max:50'],
            'middle_name' => [],
            'last_name' => ['required', 'max:30'],
            'email' => ['required', 'email:rfc,dns'],
            'primary_phone' => ['sometimes'],
            'alternate_phone' => ['sometimes'],
            'gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            'client_id' => 'required',
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
        ];
    }

    public function handle($data)
    {
        $id = Uuid::new();
        ($this->getAggregate)::retrieve($id)->create($data)->persist();

        return ($this->getModel())::findOrFail($id);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->validated(),
        );
    }
}
