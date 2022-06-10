<?php

namespace App\StorableEvents\Clients\Activity\Campaigns;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class EmailTemplateAssignedToEmailCampaign extends ShouldBeStored
{
    public $client;
    public $template;
    public $campaign;
    public $user;

    public function __construct(string $client, string $template, string $campaign, string $user)
    {
        $this->client = $client;
        $this->template = $template;
        $this->campaign = $campaign;
        $this->user = $user;
    }
}
