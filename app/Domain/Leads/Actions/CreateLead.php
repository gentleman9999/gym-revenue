<?php

namespace App\Domain\Leads\Actions;

use App\Domain\Leads\LeadAggregate;
use App\Domain\Leads\Models\Lead;
use App\Support\Uuid;
use Illuminate\Http\RedirectResponse;
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
            'first_name' => ['required', 'max:50'],
            'middle_name' => [],
            'last_name' => ['required', 'max:30'],
            'email' => ['required', 'email:rfc,dns'],
            'primary_phone' => ['sometimes'],
            'alternate_phone' => ['sometimes'],
            'gr_location_id' => ['required', 'exists:locations,gymrevenue_id'],
            'lead_source_id' => ['required', 'exists:lead_sources,id'],
            'lead_type_id' => ['required', 'exists:lead_types,id'],
//            'client_id' => 'required',
            'profile_picture' => 'sometimes',
            'profile_picture.uuid' => 'sometimes|required',
            'profile_picture.key' => 'sometimes|required',
            'profile_picture.extension' => 'sometimes|required',
            'profile_picture.bucket' => 'sometimes|required',
            'gender' => 'sometimes|required',
            'date_of_birth' => 'sometimes|required',
            'opportunity' => 'sometimes|required',
            'owner_user_id' => 'sometimes|required|exists:users,id',
            'lead_status' => 'sometimes|required|nullable|exists:lead_statuses,id',
            'notes' => 'nullable|array',
        ];
    }

    public function handle(array $data)
    {
        $id = Uuid::new();//we should use uuid here
        $data['agreement_number'] = floor(time() - 99999999);
        $data['client_id'] = auth()->user()->currentClientId();
        LeadAggregate::retrieve($id)->create($data)->persist();

        return Lead::findOrFail($id);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('leads.create', Lead::class);
    }

    public function asController(ActionRequest $request)
    {
        $lead = $this->handle(
            $request->validated(),
        );

        if ($request->user()) {
            LeadAggregate::retrieve($lead->id)->claim($request->user()->id)->persist();
        }

        return $lead;
    }

    public function htmlResponse(Lead $lead): RedirectResponse
    {
        Alert::success("Lead '{$lead->name}' was created")->flash();

        return Redirect::route('data.leads.edit', $lead->id);
    }
}
