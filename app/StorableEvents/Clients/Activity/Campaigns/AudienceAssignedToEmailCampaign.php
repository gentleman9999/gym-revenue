<?php

namespace App\StorableEvents\Clients\Activity\Campaigns;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class AudienceAssignedToEmailCampaign extends ShouldBeStored
{
    public $client, $audience, $campaign, $user;

    public function __construct($client, $audience, $campaign, $user)
    {
        $this->client = $client;
        $this->audience = $audience;
        $this->campaign = $campaign;
        $this->user = $user;
    }
}
