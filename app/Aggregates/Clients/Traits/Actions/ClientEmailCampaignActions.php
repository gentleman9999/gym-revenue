<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignCompleted;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignCreated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignLaunched;
use App\StorableEvents\Clients\Activity\Campaigns\EmailCampaignUpdated;
use App\StorableEvents\Clients\Activity\Campaigns\EmailSent;
use DateTime;

trait ClientEmailCampaignActions
{
    public function createNewEmailCampaign(string $campaign_id, string $created_by = null)
    {
        $this->recordThat(new EmailCampaignCreated($this->uuid(), $campaign_id, $created_by));

        return $this;
    }

    public function updateEmailCampaign(string $campaign_id, string $updated_by, string $field, string $new_value, string $old_value = null)
    {
        $this->recordThat(new EmailCampaignUpdated($this->uuid(), $campaign_id, $updated_by, $field, $new_value, $old_value));

        return $this;
    }

    public function launchEmailCampaign(string $campaign_id, string $launch_date, string $launched_by_user)
    {
        $this->recordThat(new EmailCampaignLaunched($this->uuid(), $campaign_id, $launch_date, $launched_by_user));

        return $this;
    }

    public function emailSent(string $campaign_id, array $sent_to, DateTime $sent_at, bool $isCampaign = false)
    {
        $this->recordThat(new EmailSent($this->uuid(), $campaign_id, $sent_to, $sent_at, $isCampaign));

        return $this;
    }

    public function completeEmailCampaign(string $campaign_id, DateTime $completed_date)
    {
        $this->recordThat(new EmailCampaignCompleted($this->uuid(), $campaign_id, $completed_date));

        return $this;
    }
}
