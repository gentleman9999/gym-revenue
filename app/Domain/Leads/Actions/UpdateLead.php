<?php

namespace App\Domain\Leads\Actions;

use App\Domain\Leads\LeadAggregate;
use App\Domain\Leads\Models\Lead;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
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
            'first_name' => ['sometimes', 'required', 'max:50'],
            'middle_name' => ['sometimes', ],
            'last_name' => ['sometimes', 'required', 'max:30'],
            'email' => ['sometimes', 'required', 'email:rfc,dns'],
            'primary_phone' => ['sometimes'],
            'alternate_phone' => ['sometimes'],
            'gr_location_id' => ['sometimes', 'required', 'exists:locations,gymrevenue_id'],
            'lead_source_id' => ['sometimes', 'required', 'exists:lead_sources,id'],
            'lead_type_id' => ['sometimes', 'required', 'exists:lead_types,id'],
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
            'notes.note' => 'nullable|required_with:notes.title|max:2',
            'notes.title' => 'nullable|required_with:notes.note',
//            'notes.note' => 'nullable|required_with:notes.title|max:2',
        ];
    }

    public function handle(Lead $lead, array $data)
    {
        LeadAggregate::retrieve($lead->id)->update($data)->persist();

        return $lead->refresh();
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('leads.update', Lead::class);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
    }

    public function asController(ActionRequest $request, Lead $lead)
    {
        $data = $request->validated();
        $lead = $this->handle(
            $lead,
            $data,
        );

        if ($request->user()) {
            AssignLeadToRep::run($lead, $request->user());
        }

        return $lead->refresh();
    }

    public function htmlResponse(Lead $lead): RedirectResponse
    {
        Alert::success("Lead '{$lead->name}' was updated")->flash();

        return Redirect::route('data.leads.edit', $lead->id);
    }
}
