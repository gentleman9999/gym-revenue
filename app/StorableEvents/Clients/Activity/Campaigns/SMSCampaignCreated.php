<?php

namespace App\StorableEvents\Clients\Activity\Campaigns;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SMSCampaignCreated extends ShouldBeStored
{
    public $client;
    public $template;
    public $created;

    public function __construct(string $client, string $template, string $created)
    {
        $this->client = $client;
        $this->template = $template;
        $this->created = $created;
    }
}
