<?php

namespace App\Domain\Leads\Actions;

use App\Domain\Leads\LeadAggregate;
use App\Domain\Leads\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class DeleteLead
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
            //no rules since we only accept an id route param, which is validated in the route definition
        ];
    }

    public function handle(Lead $lead, $current_user)
    {
        LeadAggregate::retrieve($lead->id)->delete()->persist();

        return $lead;
    }

    public function authorize(ActionRequest $request): bool
    {
        $current_user = $request->user();

        return $current_user->can('leads.delete', Lead::class);
    }

    public function asController(Request $request, $id)
    {
        return $this->handle(
            $id,
            $request->user(),
        );
    }

    public function htmlResponse(Lead $lead): RedirectResponse
    {
        Alert::success("Lead '{$lead->name}' was deleted")->flash();
//        return Redirect::route('data.leads');
        return Redirect::back();
    }
}
