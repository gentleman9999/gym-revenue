<?php

namespace App\Actions\Endusers\Leads;

use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class BatchUpsertLeadApi
{
    use AsAction;

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
            '*.lead_source_id' => ['required', 'exists:lead_sources,id'],
            '*.lead_type_id' => ['required', 'exists:lead_types,id'],
            '*.client_id' => 'required',
            '*.profile_picture' => 'sometimes',
            '*.profile_picture.uuid' => 'sometimes|required',
            '*.profile_picture.key' => 'sometimes|required',
            '*.profile_picture.extension' => 'sometimes|required',
            '*.profile_picture.bucket' => 'sometimes|required',
            '*.gender' => 'sometimes|required',
            '*.date_of_birth' => 'sometimes|required',
            '*.opportunity' => 'sometimes|required',
            '*.lead_owner' => 'sometimes|required|exists:users,id',
            '*.lead_status' => 'sometimes|required|nullable|exists:lead_statuses,id',
            '*.notes' => 'nullable|array',
            '*.external_id' => 'required',
        ];
    }

    public function handle($data)
    {
        $response = [];

        foreach ($data as $item) {
            try {
                $response[] = UpsertLeadApi::run($item);
            } catch (\Exception $e) {
                $response[] = $e;
            }
        }

        return $response;
    }

    public function asController(ActionRequest $request)
    {
        $member = $this->handle(
            $request->validated(),
        );

        return $member;
    }
}
