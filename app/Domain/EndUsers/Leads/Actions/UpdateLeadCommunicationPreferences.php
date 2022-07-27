<?php

namespace App\Domain\EndUsers\Leads\Actions;

use App\Domain\EndUsers\Actions\UpdateEndUserCommunicationPreferences;
use App\Domain\EndUsers\EndUserAggregate;
use App\Domain\EndUsers\Leads\LeadAggregate;
use App\Domain\EndUsers\Leads\Projections\Lead;
use App\Domain\EndUsers\Projections\EndUser;
use Illuminate\Http\Request;

class UpdateLeadCommunicationPreferences extends UpdateEndUserCommunicationPreferences
{
    public function asController(Request $request, Lead $lead)
    {
        $this->handle(
            $lead,
            $request->validated(),
        );
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
