<?php

namespace App\StorableEvents\Clients\Activity\Campaigns;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class EmailCampaignUpdated extends ShouldBeStored
{
    public $client;
    public $campaign;
    public $updated;
    public $field;
    public $new;
    public $old;

    public function __construct(string $client, string $campaign, string $updated, string $field, string $new, string $old)
    {
        $this->client = $client;
        $this->campaign = $campaign;
        $this->updated = $updated;
        $this->field = $field;
        $this->old = $old;
        $this->new = $new;
    }
}
