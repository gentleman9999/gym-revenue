<?php

namespace App\StorableEvents\Clients\Activity\Campaigns;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SmsCampaignLaunched extends ShouldBeStored
{
    public $client;
    public $campaign;
    public $date;
    public $user;

    public function __construct(string $client, string $campaign, string $date, string $user)
    {
        $this->client = $client;
        $this->campaign = $campaign;
        $this->date = $date;
        $this->user = $user;
    }
}
