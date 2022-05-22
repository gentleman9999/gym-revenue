<?php

namespace App\Aggregates\Clients\Traits;

use App\Models\Clients\Features\CommAudience;
use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Clients\Features\SmsCampaigns;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplates;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceUnAssignedFromEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceUnAssignedFromSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignCompleted;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignLaunched;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignUpdated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateUnAssignedFromEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignCompleted;
use App\StorableEvents\Clients\Activity\Campaigns\SMSCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignLaunched;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignUpdated;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateAssignedToSMSCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateUnAssignedFromSMSCampaign;
use App\StorableEvents\Clients\Activity\GatewayProviders\GatewayIntegrationCreated;
use App\StorableEvents\Clients\Activity\Users\ClientUserStoppedBeingImpersonated;
use App\StorableEvents\Clients\Activity\Users\ClientUserWasImpersonated;
use App\StorableEvents\Clients\Comms\AudienceCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateUpdated;
use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
use App\StorableEvents\Clients\Comms\SmsTemplateUpdated;
use App\StorableEvents\Clients\DefaultClientTeamCreated;
use App\StorableEvents\Clients\PrefixCreated;
use App\StorableEvents\Clients\TeamCreated;

trait ClientApplies
{
    protected string $date_format = 'Y-m-d H:i:s';

    public function applyDefaultClientTeamCreated(DefaultClientTeamCreated $event)
    {
        $this->default_team = $event->team;
    }

    public function applyPrefixCreated(PrefixCreated $event)
    {
        $this->team_prefix = $event->prefix;
    }

    public function applyTeamCreated(TeamCreated $event)
    {
        $this->teams[$event->team] = $event->name;
    }

    public function applyAudienceCreated(AudienceCreated $event)
    {
        $history = [
            'type' => 'Audience Created',
            'name' => $event->name,
            'slug' => $event->slug,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->comm_history[] = $history;
    }

    public function applyAudienceAssignedToEmailCampaign(AudienceAssignedToEmailCampaign $event)
    {
        $history = [
            'type' => 'Audience Assigned to a Campaign',
            'template_id' => $event->audience,
            'campaign_id' => $event->campaign,
            'model' => CommAudience::class,
            'assign_model' => EmailCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->comm_history[] = $history;
    }

    public function applyAudienceAssignedToSmsCampaign(AudienceAssignedToSmsCampaign $event)
    {
        $history = [
            'type' => 'Audience Assigned to a Campaign',
            'template_id' => $event->audience,
            'campaign_id' => $event->campaign,
            'model' => CommAudience::class,
            'assign_model' => SmsCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->comm_history[] = $history;
    }

    public function applyAudienceUnAssignedFromEmailCampaign(AudienceUnAssignedFromEmailCampaign $event)
    {
        $history = [
            'type' => 'Audience UnAssigned from a Campaign',
            'template_id' => $event->audience,
            'campaign_id' => $event->campaign,
            'model' => CommAudience::class,
            'assign_model' => EmailCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->comm_history[] = $history;
    }

    public function applyAudienceUnAssignedFromSmsCampaign(AudienceUnAssignedFromSmsCampaign $event)
    {
        $history = [
            'type' => 'Audience UnAssigned from a Campaign',
            'template_id' => $event->audience,
            'campaign_id' => $event->campaign,
            'model' => CommAudience::class,
            'assign_model' => SmsCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->comm_history[] = $history;
    }

    public function applyEmailTemplateCreated(EmailTemplateCreated $event)
    {
        $history = [
            'type' => 'Email Template Created',
            'template_id' => $event->template,
            'model' => EmailTemplates::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->created == 'auto') ? 'Auto Generated' : $event->created;
        $this->comm_history[] = $history;
    }

    public function applyEmailTemplateUpdated(EmailTemplateUpdated $event)
    {
        $history = [
            'type' => 'Email Template Updated',
            'template_id' => $event->template,
            'model' => EmailTemplates::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = $event->updated;
        $this->comm_history[] = $history;
    }

    public function applyEmailCampaignCreated(EmailCampaignCreated $event)
    {
        $history = [
            'type' => 'Email Campaign Created',
            'template_id' => $event->template,
            'model' => EmailCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->created == 'auto') ? 'Auto Generated' : $event->created;
        $this->comm_history[] = $history;
    }

    public function applyEmailCampaignUpdated(EmailCampaignUpdated $event)
    {
        $history = [
            'type' => 'Email Campaign Updated',
            'campaign_id' => $event->campaign,
            'model' => EmailCampaigns::class,
            'field' => $event->field,
            'old_value' => $event->old,
            'new_value' => $event->new,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->updated == 'auto') ? 'Auto Generated' : $event->updated;
        $this->comm_history[] = $history;
    }

    public function applyEmailTemplateAssignedToEmailCampaign(EmailTemplateAssignedToEmailCampaign $event)
    {
        $history = [
            'type' => 'Email Template Assigned to a Campaign',
            'template_id' => $event->template,
            'campaign_id' => $event->campaign,
            'model' => EmailTemplates::class,
            'assign_model' => EmailCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->comm_history[] = $history;
    }

    public function applyEmailTemplateUnAssignedFromEmailCampaign(EmailTemplateUnAssignedFromEmailCampaign $event)
    {
        $history = [
            'type' => 'Email Template UnAssigned from a Campaign',
            'template_id' => $event->template,
            'campaign_id' => $event->campaign,
            'model' => EmailTemplates::class,
            'assign_model' => EmailCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->comm_history[] = $history;
    }

    public function applyEmailCampaignLaunched(EmailCampaignLaunched $event)
    {
        $history = [
            'type' => 'Email Campaign Launched',
            'campaign_id' => $event->campaign,
            'model' => EmailCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->comm_history[] = $history;
    }

    public function applyEmailCampaignCompleted(EmailCampaignCompleted $event)
    {
        $history = [
            'type' => 'Email Campaign Completed',
            'campaign_id' => $event->campaign,
            'model' => EmailCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = 'Auto Generated';
        $this->comm_history[] = $history;
    }

    public function applySMSTemplateCreated(SMSTemplateCreated $event)
    {
        $history = [
            'type' => 'SMS Template Created',
            'template_id' => $event->template,
            'model' => SmsTemplates::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->created == 'auto') ? 'Auto Generated' : $event->created;
        $this->comm_history[] = $history;
    }

    public function applySmsTemplateUpdated(SmsTemplateUpdated $event)
    {
        $history = [
            'type' => 'SMS Template Updated',
            'template_id' => $event->template,
            'model' => SmsTemplates::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = $event->updated;
        $this->comm_history[] = $history;
    }

    public function applySMSCampaignCreated(SMSCampaignCreated $event)
    {
        $history = [
            'type' => 'SMS Campaign Created',
            'template_id' => $event->template,
            'model' => SmsCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->created == 'auto') ? 'Auto Generated' : $event->created;
        $this->comm_history[] = $history;
    }

    public function applySmsCampaignUpdated(SmsCampaignUpdated $event)
    {
        $history = [
            'type' => 'SMS Campaign Updated',
            'campaign_id' => $event->campaign,
            'model' => SmsCampaigns::class,
            'field' => $event->field,
            'old_value' => $event->old,
            'new_value' => $event->new,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->updated == 'auto') ? 'Auto Generated' : $event->updated;
        $this->comm_history[] = $history;
    }

    public function applySMSTemplateAssignedToSMSCampaign(SMSTemplateAssignedToSMSCampaign $event)
    {
        $history = [
            'type' => 'SMS Template Assigned to a Campaign',
            'template_id' => $event->template,
            'campaign_id' => $event->campaign,
            'model' => SmsTemplates::class,
            'assign_model' => SmsCampaigns::class,
            'date' => $event->metaData()['created-at'],
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->comm_history[] = $history;
    }

    public function applySMSTemplateUnAssignedFromSMSCampaign(SMSTemplateUnAssignedFromSMSCampaign $event)
    {
        $history = [
            'type' => 'SMS Template UnAssigned from a Campaign',
            'template_id' => $event->template,
            'campaign_id' => $event->campaign,
            'model' => SmsTemplates::class,
            'assign_model' => SmsCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->comm_history[] = $history;
    }

    public function applySmsCampaignLaunched(SmsCampaignLaunched $event)
    {
        $history = [
            'type' => 'SMS Campaign Launched',
            'campaign_id' => $event->campaign,
            'model' => SmsCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->comm_history[] = $history;
    }

    public function applySmsCampaignCompleted(SmsCampaignCompleted $event)
    {
        $history = [
            'type' => 'SMS Campaign Completed',
            'campaign_id' => $event->campaign,
            'model' => SmsCampaigns::class,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = 'Auto Generated';
        $this->comm_history[] = $history;
    }

    public function applyGatewayIntegrationCreated(GatewayIntegrationCreated $event)
    {
        $history = [
            'activity' => 'Gateway Integration Created',
            'type' => $event->type,
            'gateway_slug' => $event->slug,
            'date' => date($this->date_format, strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->user;
        $this->provider_history[] = $history;
    }

    public function applyClientUserWasImpersonated(ClientUserWasImpersonated $event)
    {
        $this->employee_activity[] = [
            'event' => 'user-was-impersonated',
            'employee' => $event->employee,
            'date' => $event->date,
            'impersonator' => $event->invader,
        ];
    }

    public function applyClientUserStoppedBeingImpersonated(ClientUserStoppedBeingImpersonated $event)
    {
        $this->employee_activity[] = [
            'event' => 'user-stopped-being-impersonated',
            'employee' => $event->employee,
            'date' => $event->date,
            'impersonator' => $event->invader,
        ];
    }
}
