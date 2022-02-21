<?php

namespace App\StorableEvents\Clients\Activity\GatewayProviders\Email;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserSentATestEmail extends ShouldBeStored
{
    public function __construct()
    {
    }
}
