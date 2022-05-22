<?php

namespace App\StorableEvents\Clients\Activity\GatewayProviders\SMS;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserSentATestSMS extends ShouldBeStored
{
    public $client;
    public $user;
    public $template;

    public function __construct($client, $user, $template)
    {
        $this->client = $client;
        $this->user = $user;
        $this->template = $template;
    }
}
