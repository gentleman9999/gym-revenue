<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateAssignedToEmailCampaign;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateUpdated;

trait ClientEmailTemplateActions
{
    public function createNewEmailTemplate(string $template_id, string $created_by = null)
    {
        $this->recordThat(new EmailTemplateCreated($this->uuid(), $template_id, $created_by));
        return $this;
    }

    public function updateEmailTemplate(string $template_id, string $updated_by, array $old_vals, array $new_vals)
    {
        $this->recordThat(new EmailTemplateUpdated($this->uuid(), $template_id, $updated_by, $old_vals, $new_vals));
        return $this;
    }

    public function assignEmailTemplateToCampaign($template_id, $campaign_id, $created_by_user_id)
    {
        $this->recordThat(new EmailTemplateAssignedToEmailCampaign($this->uuid(), $template_id, $campaign_id, $created_by_user_id));
        return $this;
    }

    public function unassignEmailTemplateFromCampaign($template_id, $campaign_id, $updated_by_user_id)
    {
        $this->recordThat(new EmailTemplateUnAssignedFromEmailCampaign($this->uuid(), $template_id, $campaign_id, $updated_by_user_id));
        return $this;
    }
}
