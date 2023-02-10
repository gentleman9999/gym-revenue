<?php

declare(strict_types=1);

namespace App\StorableEvents\Clients\Activity\GatewayProviders\Email;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserSentATestEmail extends ShouldBeStored
{
    public $client;
    public $user;
    public $subject;
    public $template;

    public function __construct(string $client, string $user, string $subject, string $template)
    {
        $this->client   = $client;
        $this->user     = $user;
        $this->subject  = $subject;
        $this->template = $template;
    }
}
