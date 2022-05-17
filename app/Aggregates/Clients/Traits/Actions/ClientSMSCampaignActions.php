<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignUpdated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignCompleted;
use App\StorableEvents\Clients\Activity\Campaigns\SMSCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignLaunched;
use App\StorableEvents\Clients\Activity\Campaigns\SmsSent;
use App\StorableEvents\Clients\Activity\Campaigns\SmsCampaignUpdated;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateUpdated;
use DateTime;

trait ClientSMSCampaignActions
{
    public function createNewSMSCampaign(string $template_id, string $created_by = null)
    {
        $this->recordThat(new SMSCampaignCreated($this->uuid(), $template_id, $created_by));
        return $this;
    }

    public function updateSmsCampaign(string $campaign_id, string $updated_by, string $field, string $new_value, string $old_value = null)
    {
        $this->recordThat(new SmsCampaignUpdated($this->uuid(), $campaign_id, $updated_by, $field, $new_value, $old_value));
        return $this;
    }

    public function launchSMSCampaign(string $campaign_id, string $launch_date, string $launched_by_user)
    {
        $this->recordThat(new SmsCampaignLaunched($this->uuid(), $campaign_id, $launch_date, $launched_by_user));
        return $this;
    }

    public function smsSent(string $campaign_id, array $sent_to, DateTime $sent_at, bool $isCampaign = false)
    {
        $this->recordThat(new SmsSent($this->uuid(), $campaign_id, $sent_to, $sent_at, $isCampaign));
        return $this;
    }

    public function completeSmsCampaign(string $campaign_id, DateTime $completed_date)
    {
        $this->recordThat(new SmsCampaignCompleted($this->uuid(), $campaign_id, $completed_date));
        return $this;
    }
}
