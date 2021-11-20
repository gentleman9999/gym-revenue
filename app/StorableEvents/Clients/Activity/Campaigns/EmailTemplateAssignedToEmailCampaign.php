<?php

namespace App\StorableEvents\Clients\Activity\Campaigns;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class EmailTemplateAssignedToEmailCampaign extends ShouldBeStored
{
    public $client, $template, $campaign, $user;

    public function __construct($client, $template, $campaign, $user)
    {
        $this->client = $client;
        $this->template = $template;
        $this->campaign = $campaign;
        $this->user = $user;
    }
}
