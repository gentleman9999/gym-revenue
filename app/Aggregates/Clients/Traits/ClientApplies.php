<?php

namespace App\Aggregates\Clients\Traits;

use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SMSCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateAssignedToSMSCampaign;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
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

    public function applyEmailTemplateCreated(EmailTemplateCreated $event)
    {
        $history = [
            'type' => 'Email Template Created',
            'template_id' => $event->template,
            'date' => date('Y-m-d', strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->created == 'auto') ? 'Auto Generated' : $event->created;
        $this->comm_history[] = $history;
    }
    public function applyEmailCampaignCreated(EmailCampaignCreated $event)
    {
        $history = [
            'type' => 'Email Campaign Created',
            'template_id' => $event->template,
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
            'date' => date('Y-m-d', strtotime($event->metaData()['created-at'])),
        ];
        $history['by'] = ($event->created == 'auto') ? 'Auto Generated' : $event->created;
        $this->comm_history[] = $history;
    }
    public function applySMSCampaignCreated(SMSCampaignCreated $event)
    {
        $history = [
            'type' => 'SMS Campaign Created',
            'template_id' => $event->template,
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
            'date' => $event->metaData()['created-at'],
        ];
        $history['by'] = ($event->user == 'auto') ? 'Auto Generated' : $event->created;
        $this->comm_history[] = $history;
    }
}
