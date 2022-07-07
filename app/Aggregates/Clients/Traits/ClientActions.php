<?php

namespace App\Aggregates\Clients\Traits;

use App\Aggregates\Clients\Traits\Actions\ClientAudienceActions;
use App\Aggregates\Clients\Traits\Actions\ClientClassificationActions;
use App\Aggregates\Clients\Traits\Actions\ClientEmailCampaignActions;
use App\Aggregates\Clients\Traits\Actions\ClientEmailTemplateActions;
use App\Aggregates\Clients\Traits\Actions\ClientGatewayActions;
use App\Aggregates\Clients\Traits\Actions\ClientLeadActions;
use App\Aggregates\Clients\Traits\Actions\ClientLocationsActions;
use App\Aggregates\Clients\Traits\Actions\ClientReminderActions;
use App\Aggregates\Clients\Traits\Actions\ClientRoleActions;
use App\Aggregates\Clients\Traits\Actions\ClientServicesActions;
use App\Aggregates\Clients\Traits\Actions\ClientSMSCampaignActions;
use App\Aggregates\Clients\Traits\Actions\ClientSMSTemplateActions;
use App\Aggregates\Clients\Traits\Actions\ClientTeamActions;
use App\Aggregates\Clients\Traits\Actions\ClientTrialMembershipActions;
use App\Aggregates\Clients\Traits\Actions\ClientUserActions;

trait ClientActions
{
    use ClientTeamActions;
    use ClientSMSTemplateActions;
    use ClientSMSCampaignActions;
    use ClientEmailTemplateActions;
    use ClientEmailCampaignActions;
    use ClientAudienceActions;
    use ClientGatewayActions;
    use ClientLeadActions;
    use ClientServicesActions;
    use ClientTrialMembershipActions;
    use ClientRoleActions;
    use ClientReminderActions;
    use ClientClassificationActions;
    use ClientUserActions;
    use ClientLocationsActions;
}
