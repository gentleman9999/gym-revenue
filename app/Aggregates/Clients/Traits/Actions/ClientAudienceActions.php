<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\EmailTemplateAssignedToEmailCampaign;
use App\StorableEvents\Clients\Comms\AudienceCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateCreated;
use App\StorableEvents\Clients\Comms\EmailTemplateUpdated;

trait ClientAudienceActions
{
    public function createAudience(string $name, string $slug, /*string $default_email, string $from_name,*/ string $created_by_user_id)
    {
        $this->recordThat(new AudienceCreated($this->uuid(), $name, $slug, /*$default_email, $from_name,*/ $created_by_user_id));
        return $this;
    }

    public function assignAudienceToEmailCampaign($audience_id, $campaign_id, $updated_by_user_id)
    {
        $this->recordThat(new AudienceAssignedToEmailCampaign($this->uuid(), $audience_id, $campaign_id, $updated_by_user_id));
        return $this;
    }

    public function assignAudienceToSMSCampaign($audience_id, $campaign_id, $updated_by_user_id)
    {
        $this->recordThat(new AudienceAssignedToSmsCampaign($this->uuid(), $audience_id, $campaign_id, $updated_by_user_id));
        return $this;
    }

    public function unassignAudienceFromEmailCampaign($audience_id, $campaign_id, $updated_by_user_id)
    {
        $this->recordThat(new AudienceUnAssignedFromEmailCampaign($this->uuid(), $audience_id, $campaign_id, $updated_by_user_id));
        return $this;
    }

    public function unassignAudienceFromSMSCampaign($audience_id, $campaign_id, $updated_by_user_id)
    {
        $this->recordThat(new AudienceUnAssignedFromSmsCampaign($this->uuid(), $audience_id, $campaign_id, $updated_by_user_id));
        return $this;
    }
}
