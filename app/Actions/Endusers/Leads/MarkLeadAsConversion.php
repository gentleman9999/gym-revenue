<?php

namespace App\Actions\Endusers\Leads;

use App\Aggregates\Endusers\LeadAggregate;
use App\Aggregates\Endusers\MemberAggregate;
use App\Models\Endusers\Lead;
use App\Models\Endusers\Member;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class MarkLeadAsConversion
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
            'member_id' => 'required|int',
            'lead_id' => 'required|int',
        ];
    }

    public function handle(Lead $lead, Member $member): Lead
    {
        LeadAggregate::retrieve($lead['id'])->convert($lead['id']);
        MemberAggregate::retrieve($member['id'])->convert($member['id']);

        return Lead::findOrFail($lead['id']);
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('leads.edit', Lead::class);
    }

    public function asController(ActionRequest $request)
    {
        $lead = $this->handle(
            $request->validated(),
        );

        Alert::success("Lead '{$lead->name}' was converted")->flash();

        return Redirect::route('data.leads');
    }
}
