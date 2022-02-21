<?php

namespace App\StorableEvents\Clients\Activity\GatewayProviders\Email;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserSentATestEmail extends ShouldBeStored
{
    public $client, $user, $subject, $template;
    public function __construct($client, $user, $subject, $template)
    {
        $this->client = $client;
        $this->user = $user;
        $this->subject = $subject;
        $this->template = $template;
    }
}
