<?php

namespace App\Domain\EndUsers\Leads\Actions;

use App\Domain\EndUsers\Actions\UpdateEndUser;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Leads\LeadAggregate;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Projections\EndUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class UpdateLead extends UpdateEndUser
{
    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        $base_rules = parent::rules();

        return array_merge($base_rules, [
            'lead_source_id' => ['sometimes', 'exists:lead_sources,id'],
            'lead_type_id' => ['sometimes', 'exists:lead_types,id'],
            'lead_status' => 'sometimes|required|nullable|exists:lead_statuses,id',
        ]);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('leads.update', Lead::class);
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

    protected static function getModel(): EndUser
    {
        return new Lead();
    }

    protected static function getAggregate(): EndUserAggregate
    {
        return new LeadAggregate();
    }
}
