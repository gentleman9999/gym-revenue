<?php

namespace App\Domain\Users\Events;

use Spatie\EventSourcing\StoredEvents\ShouldBeStored;

class UserReceivedTextMsg extends ShouldBeStored
{
    public $template;
    public $response;
    public $client;

    public function __construct(string $template, string $response, $client = null)
    {
        $this->template = $template;
        $this->response = $response;
        $this->client = $client;
    }
}
