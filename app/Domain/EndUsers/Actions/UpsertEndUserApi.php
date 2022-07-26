<?php

namespace App\Domain\EndUsers\Actions;

use App\Support\Uuid;
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
            'first_name' => ['sometimes', 'max:50'],
            'middle_name' => ['sometimes'],
            'last_name' => ['sometimes', 'max:30'],
            'email' => ['sometimes', 'email:rfc,dns'],
            'primary_phone' => ['sometimes'],
            'alternate_phone' => ['sometimes'],
            'gr_location_id' => ['sometimes', 'exists:locations,gymrevenue_id'],
            'client_id' => 'sometimes',
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

    public function handle(array $data, $current_user = null)
    {
        $id = Uuid::new();//we should use uuid here

        if (array_key_exists('external_id', $data)) {
            $lead = ($this->getModel())::whereEmail($data['email'])
                ->orWhere('external_id', $data['external_id'])
                ->first();
        } else {
            $lead = ($this->getModel())::whereEmail($data['email'])
                ->first();
        }
        if (is_null($lead)) {
            $data['agreement_number'] = floor(time() - 99999999);
            $aggy = ($this->getAggregate())::retrieve($id);
            $aggy->create($data);
        } else {
            $old_data = $lead->toArray();
            $aggy = ($this->getAggregate())::retrieve($old_data['id']);
            $aggy->update($data);
        }

        try {
            $aggy->persist();
        } catch (\Exception $e) {
            dd($e);
        }

        return ($this->getModel())::findOrFail($id);
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(
            $request->validated(),
        );
    }
}
