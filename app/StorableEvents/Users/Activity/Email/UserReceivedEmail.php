<?php

namespace App\StorableEvents\Users\Activity\Email;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserReceivedEmail extends ShouldBeStored
{
    public $user;
    public $subject;
    public $template;
    public $response;
    public $client;

    public function __construct(string $user, string $subject, string $template, string $response, $client = null)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->template = $template;
        $this->response = $response;
        $this->client = $client;
    }
}
