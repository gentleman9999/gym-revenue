<?php

namespace App\Aggregates\Clients;

use App\Aggregates\Clients\Traits\ClientActions;
use App\Aggregates\Clients\Traits\ClientApplies;
use App\Aggregates\Clients\Traits\ClientGetters;
use App\Exceptions\Clients\ClientAccountException;
use App\Models\UserDetails;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceUnAssignedFromEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceUnAssignedFromSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignLaunched;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignUpdated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateUnAssignedFromEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SMSCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignLaunched;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignUpdated;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateAssignedToSMSCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateUnAssignedFromSMSCampaign;
use App\StorableEvents\Clients\CapeAndBayUsersAssociatedWithClientsNewDefaultTeam;
use App\StorableEvents\Clients\UserDeleted;
use App\StorableEvents\Clients\Comms\AudienceCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateUpdated;
use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
use App\StorableEvents\Clients\Comms\SmsTemplateUpdated;
use App\StorableEvents\Clients\DefaultClientTeamCreated;
use App\StorableEvents\Clients\TeamCreated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ClientAggregate extends AggregateRoot
{
    use ClientGetters, ClientApplies, ClientActions;

    protected string $default_team = '';
    protected array $teams = [];

    protected static bool $allowConcurrency = true;

}
