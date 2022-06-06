<?php

namespace App\Actions\Endusers\Leads;

use App\Aggregates\Endusers\LeadAggregate;
use App\Helpers\Uuid;
use App\Models\Endusers\Lead;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateLeadApi
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
            'first_name' => ['required', 'max:50'],
            'middle_name' => [],
            'last_name' => ['required', 'max:30'],
            'email' => ['required', 'email:rfc,dns'],
            'primary_phone' => ['sometimes'],
            'alternate_phone' => ['sometimes'],
            'gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            'lead_source_id' => ['required', 'exists:lead_sources,id'],
            'lead_type_id' => ['required', 'exists:lead_types,id'],
            'client_id' => 'required',
            'profile_picture' => 'sometimes',
            'profile_picture.uuid' => 'sometimes|required',
            'profile_picture.key' => 'sometimes|required',
            'profile_picture.extension' => 'sometimes|required',
            'profile_picture.bucket' => 'sometimes|required',
            'gender' => 'sometimes|required',
            'date_of_birth' => 'sometimes|required',
            'opportunity' => 'sometimes|required',
            'lead_owner' => 'sometimes|required|exists:users,id',
            'lead_status' => 'sometimes|required|nullable|exists:lead_statuses,id',
            'notes' => 'nullable|array',
        ];
    }

    public function handle($data, $current_user = null)
    {
        $id = Uuid::new();//we should use uuid here
        $data['id'] = $id;
        $aggy = LeadAggregate::retrieve($data['id']);
        $aggy->create($data, $current_user->id ?? 'Auto Generated');
        $aggy->joinAudience('leads', $data['client_id'], Lead::class);
        if ($current_user) {
            $aggy->claim($current_user->id, $data['client_id']);
        }

        $aggy->persist();

        return Lead::findOrFail($id);
    }

    public function asController(ActionRequest $request)
    {
        $lead = $this->handle(
            $request->validated(),
        );

        return $lead;
    }
}
