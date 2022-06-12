<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateUnAssignedFromEmailCampaign;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateThumbnailUpdated;
use App\StorableEvents\Clients\Comms\EmailTemplateUpdated;

trait ClientEmailTemplateActions
{
    public function createEmailTemplate(string $created_by_user_id, array $payload)
    {
        $this->recordThat(new EmailTemplateCreated($this->uuid(), $created_by_user_id, $payload));

        return $this;
    }

    public function updateEmailTemplate(string $updated_by_user_id, array $payload)
    {
        $this->recordThat(new EmailTemplateUpdated($this->uuid(), $updated_by_user_id, $payload));

        return $this;
    }

    public function setEmailTemplateThumbnail(string $id, string $key, string $url)
    {
        $this->recordThat(new EmailTemplateThumbnailUpdated($this->uuid(), $id, $key,  $url));

        return $this;
    }

    public function assignEmailTemplateToCampaign(string $template_id, string $campaign_id, string $created_by_user_id)
    {
        $this->recordThat(new EmailTemplateAssignedToEmailCampaign($this->uuid(), $template_id, $campaign_id, $created_by_user_id));

        return $this;
    }

    public function unassignEmailTemplateFromCampaign(string $template_id, string $campaign_id, string $updated_by_user_id)
    {
        $this->recordThat(new EmailTemplateUnAssignedFromEmailCampaign($this->uuid(), $template_id, $campaign_id, $updated_by_user_id));

        return $this;
    }
}
