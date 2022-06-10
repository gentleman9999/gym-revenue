<?php

namespace App\StorableEvents\Clients\Activity\Campaigns;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class AudienceAssignedToEmailCampaign extends ShouldBeStored
{
    public $client;
    public $audience;
    public $campaign;
    public $user;

    public function __construct(string $client, string $audience, string $campaign, string $user)
    {
        $this->client = $client;
        $this->audience = $audience;
        $this->campaign = $campaign;
        $this->user = $user;
    }
}
