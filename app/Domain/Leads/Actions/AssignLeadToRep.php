<?php

namespace App\Domain\Leads\Actions;

use App\Domain\Leads\LeadAggregate;
use App\Domain\Leads\Models\Lead;
use App\Domain\Users\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Prologue\Alerts\Facades\Alert;

class AssignLeadToRep
{
    use AsAction;

    public function handle(Lead $lead, User $user)
    {
        LeadAggregate::retrieve($lead->id)->claim($user->id)->persist();

        return $lead->refresh();
    }

    public function authorize(ActionRequest $request, Lead $lead): bool
    {
        $current_user = $request->user();

        if ($lead->owner_user_id !== null) {
            return false;
        }

        return $current_user->can('leads.contact', Lead::class);
    }

    public function asController(ActionRequest $request, Lead $lead)
    {
        return $this->handle(
            $lead,
            $request->user(),
        );
    }

    public function htmlResponse(Lead $lead): RedirectResponse
    {
        Alert::success("Lead '{$lead->name}' assigned to Rep")->flash();

        return Redirect::back();
    }
}
