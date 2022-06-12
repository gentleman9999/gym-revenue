<?php

namespace App\Aggregates\Clients\Traits\Actions;

use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceAssignedToSmsCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceUnAssignedFromEmailCampaign;
use App\StorableEvents\Clients\Activity\Campaigns\AudienceUnAssignedFromSmsCampaign;
use App\StorableEvents\Clients\Comms\AudienceCreated;

trait ClientAudienceActions
{
    public function createAudience(string $name, string $slug, /*string $default_email, string $from_name,*/ string $created_by_user_id)
    {
        $this->recordThat(new AudienceCreated($this->uuid(), $name, $slug, /*$default_email, $from_name,*/ $created_by_user_id));

        return $this;
    }

    public function assignAudienceToEmailCampaign(string $audience_id, string $campaign_id, string $updated_by_user_id)
    {
        $this->recordThat(new AudienceAssignedToEmailCampaign($this->uuid(), $audience_id, $campaign_id, $updated_by_user_id));

        return $this;
    }

    public function assignAudienceToSMSCampaign(string $audience_id, string $campaign_id, string $updated_by_user_id)
    {
        $this->recordThat(new AudienceAssignedToSmsCampaign($this->uuid(), $audience_id, $campaign_id, $updated_by_user_id));

        return $this;
    }

    public function unassignAudienceFromEmailCampaign(string $audience_id, string $campaign_id, string $updated_by_user_id)
    {
        $this->recordThat(new AudienceUnAssignedFromEmailCampaign($this->uuid(), $audience_id, $campaign_id, $updated_by_user_id));

        return $this;
    }

    public function unassignAudienceFromSMSCampaign(string $audience_id, string $campaign_id, string $updated_by_user_id)
    {
        $this->recordThat(new AudienceUnAssignedFromSmsCampaign($this->uuid(), $audience_id, $campaign_id, $updated_by_user_id));

        return $this;
    }
}
