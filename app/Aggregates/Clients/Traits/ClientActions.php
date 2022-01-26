<?php

namespace App\Aggregates\Clients\Traits;

use App\Aggregates\Clients\Traits\Actions\ClientAudienceActions;
use App\Aggregates\Clients\Traits\Actions\ClientEmailCampaignActions;
use App\Aggregates\Clients\Traits\Actions\ClientEmailTemplateActions;
use App\Aggregates\Clients\Traits\Actions\ClientGatewayActions;
use App\Aggregates\Clients\Traits\Actions\ClientSMSCampaignActions;
use App\Aggregates\Clients\Traits\Actions\ClientTeamActions;
use App\Aggregates\Clients\Traits\Actions\ClientSMSTemplateActions;
use App\Aggregates\Clients\Traits\Actions\ClientUserActions;

trait ClientActions
{
    use ClientTeamActions;
    use ClientUserActions;
    use ClientSMSTemplateActions;
    use ClientSMSCampaignActions;
    use ClientEmailTemplateActions;
    use ClientEmailCampaignActions;
    use ClientAudienceActions;
    use ClientGatewayActions;
}
