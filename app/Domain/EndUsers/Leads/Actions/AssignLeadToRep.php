<?php

namespace App\Domain\EndUsers\Leads\Actions;

use App\Domain\EndUsers\Actions\AssignEndUserToRep;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Leads\LeadAggregate;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Projections\EndUser;
use App\Http\Middleware\InjectClientId;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Lorisleiva\Actions\ActionRequest;
use Prologue\Alerts\Facades\Alert;

class AssignLeadToRep extends AssignEndUserToRep
{
    public function authorize(ActionRequest $request, Lead $lead): bool
    {
        $current_user = $request->user();

        if ($lead->owner_user_id !== null) {
            return false;
        }

        return $current_user->can('leads.contact', Lead::class);
    }

    public function getControllerMiddleware(): array
    {
        return [InjectClientId::class];
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

    protected static function getModel(): EndUser
    {
        return new Lead();
    }

    protected static function getAggregate(): EndUserAggregate
    {
        return new LeadAggregate();
    }
}
