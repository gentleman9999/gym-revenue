<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateAssignedToSMSCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\SMSTemplateUnAssignedFromSMSCampaign;
use App\StorableEvents\Clients\Comms\SMSTemplateCreated;
use App\StorableEvents\Clients\Comms\SmsTemplateUpdated;

trait ClientSMSTemplateActions
{
    public function createSMSTemplate(string $template_id, string $created_by = null)
    {
        $this->recordThat(new SMSTemplateCreated($this->uuid(), $template_id, $created_by));

        return $this;
    }

    public function updateSmsTemplate(string $template_id, string $updated_by, array $old_vals, array $new_vals)
    {
        $this->recordThat(new SmsTemplateUpdated($this->uuid(), $template_id, $updated_by, $old_vals, $new_vals));

        return $this;
    }

    public function assignSmsTemplateToCampaign($template_id, $campaign_id, $created_by_user_id)
    {
        $this->recordThat(new SMSTemplateAssignedToSMSCampaign($this->uuid(), $template_id, $campaign_id, $created_by_user_id));

        return $this;
    }

    public function unassignSmsTemplateFromCampaign($template_id, $campaign_id, $created_by_user_id)
    {
        $this->recordThat(new SMSTemplateUnAssignedFromSMSCampaign($this->uuid(), $template_id, $campaign_id, $created_by_user_id));

        return $this;
    }
}
