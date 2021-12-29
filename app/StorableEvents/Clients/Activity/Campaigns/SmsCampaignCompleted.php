<?php

namespace App\StorableEvents\Clients\Activity\Campaigns;

use DateTime;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SmsCampaignCompleted extends ShouldBeStored
{
    public string $client;
    public string $campaign;
    public DateTime $date;

    public function __construct(string $client, string $campaign, DateTime $date)
    {
        $this->client = $client;
        $this->campaign = $campaign;
        $this->date = $date;
    }
}
