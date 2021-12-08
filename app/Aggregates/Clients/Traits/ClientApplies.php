<?php

namespace App\Aggregates\Clients\Traits;

use App\Models\Clients\Features\EmailCampaigns;
use App\Models\Clients\Features\SmsCampaigns;
use App\Models\Comms\EmailTemplates;
use App\Models\Comms\SmsTemplates;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SMSCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateAssignedToSMSCampaign;
use App\StorableEvents\Clients\Comms\AudienceCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateUpdated;
use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
use App\StorableEvents\Clients\Comms\SmsTemplateUpdated;
use App\StorableEvents\Clients\DefaultClientTeamCreated;
use App\StorableEvents\Clients\TeamCreated;

trait ClientApplies
{
    public function applyDefaultClientTeamCreated(DefaultClientTeamCreated $event)
    {
        $this->default_team = $event->team;
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
            'date' => date('Y-m-d', strtotime($event->metaData()['created-at'])),
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
            'date' => date('Y-m-d', strtotime($event->metaData()['created-at'])),
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
            'date' => date('Y-m-d', strtotime($event->metaData()['created-at'])),
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
            'date' => date('Y-m-d', strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->created == 'auto') ? 'Auto Generated' : $event->created;
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
            'date' => date('Y-m-d', strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->created;
        $this->comm_history[] = $history;
    }

    public function applySMSTemplateCreated(SMSTemplateCreated $event)
    {
        $history = [
            'type' => 'SMS Template Created',
            'template_id' => $event->template,
            'model' => SmsTemplates::class,
            'date' => date('Y-m-d', strtotime($event->metaData()['created-at'])),
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
            'date' => date('Y-m-d', strtotime($event->metaData()['created-at'])),
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
            'date' => date('Y-m-d', strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->created == 'auto') ? 'Auto Generated' : $event->created;
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
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->created;
        $this->comm_history[] = $history;
    }
}
