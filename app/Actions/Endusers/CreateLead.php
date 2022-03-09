<?php

namespace App\Actions\Endusers;

use App\Aggregates\Endusers\EndUserActivityAggregate;
use App\Helpers\Uuid;
use App\Models\Endusers\Lead;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;


class CreateLead
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
            'first_name'                => ['required', 'max:50'],
            'middle_name'               => [],
            'last_name'                 => ['required', 'max:30'],
            'email'                     => ['required', 'email:rfc,dns'],
            'primary_phone'             => ['sometimes'],
            'alternate_phone'           => ['sometimes'],
            'gr_location_id'            => ['required', 'exists:locations,gymrevenue_id'],
            'lead_source_id'            => ['required', 'exists:lead_sources,id'],
            'lead_type_id'              => ['required', 'exists:lead_types,id'],
            'client_id'                 => 'required',
            'profile_picture'           => 'sometimes',
            'profile_picture.uuid'      => 'sometimes|required',
            'profile_picture.key'       => 'sometimes|required',
            'profile_picture.extension' => 'sometimes|required',
            'profile_picture.bucket'    => 'sometimes|required',
            'gender'                    => 'sometimes|required',
            'dob'                       => 'sometimes|required',
            'opportunity'               => 'sometimes|required',
            'lead_owner'                => 'sometimes|required|exists:users,id',
            'lead_status'               => 'sometimes|required|exists:lead_statuses,id',
            'notes'                     => 'nullable|string'
        ];
    }

    public function handle($data, $current_user = null)
    {
        $id = Uuid::new();//we should use uuid here
        $data['id'] = $id;
        $aggy = EndUserActivityAggregate::retrieve($data['id']);
        $aggy->createLead( $data, $current_user->id ?? 'Auto Generated');
        $aggy->joinAudience('leads', $data['client_id'], Lead::class);
        if($current_user){
            $aggy->claimLead($current_user->id, $data['client_id']);
        }

        $aggy->persist();

        return Lead::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();
        return $current_user->can('leads.create', $current_user->currentTeam()->first());
    }

    public function asController(ActionRequest $request)
    {

        $lead = $this->handle(
            $request->validated(),
            $request->user(),
        );

        Alert::success("Lead '{$lead->name}' was created")->flash();

        return Redirect::route('data.leads.edit', $lead->id);
    }

}
