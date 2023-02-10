<?php

declare(strict_types=1);

namespace App\StorableEvents\Clients\Activity\Campaigns;

use DateTime;
use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class SmsSent extends ShouldBeStored
{
    public string $client;
    public string $campaign;
    public array $sentTo;
    public DateTime $sentAt;
    public bool $isCampaign;

    public function __construct(string $client, string $campaign, array $sentTo, DateTime $sentAt, bool $isCampaign)
    {
        $this->client     = $client;
        $this->campaign   = $campaign;
        $this->sentTo     = $sentTo;
        $this->sentAt     = $sentAt;
        $this->isCampaign = $isCampaign;
    }
}
