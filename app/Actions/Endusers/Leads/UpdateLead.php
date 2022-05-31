<?php

namespace App\Actions\Endusers\Leads;

use App\Aggregates\Endusers\LeadAggregate;
use App\Models\Endusers\Lead;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class UpdateLead
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

    public function handle($data, $current_user)
    {
        $old_data = Lead::with([
            'profile_picture',
            'lead_owner',
            'lead_status',
        ])->findOrFail($data['id'])->toArray();
        $aggy = LeadAggregate::retrieve($data['id']);
        $aggy->update($data, $old_data, $current_user->id ?? 'Auto Generated');
        if ($current_user) {
            $aggy->claim($current_user->id, $data['client_id']);
        }
        $aggy->persist();

        return Lead::findOrFail($data['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('leads.update', Lead::class);
    }

    public function asController(ActionRequest $request, $id)
    {
        $data = $request->validated();
        $data['id'] = $id;
        $lead = $this->handle(
            $data,
            $request->user(),
        );

        Alert::success("Lead '{$lead->name}' was updated")->flash();

//        return Redirect::route('data.leads');
        return Redirect::back();
    }
}
