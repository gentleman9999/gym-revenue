<?php

declare(strict_types=1);

namespace App\Services;

use App\Domain\Clients\Projections\Client;
use App\Services\Abstractions\AbstractInstanceCache;
use InvalidArgumentException;
use Mailgun\Mailgun;
use Mailgun\Model\Message\SendResponse;
use Psr\Http\Message\ResponseInterface;

class MailgunService extends AbstractInstanceCache
{
    public readonly string $domain;
    public readonly string $secret;
    public readonly string $from;
    public readonly string $name;

    protected function __construct(public Client $client)
    {
        $settings = $client->getNamedGatewaySettings();

        // Required Fields
        $this->domain = $settings['mailgunDomain'] ?? throw new InvalidArgumentException('"mailgunDomain" is required');
        $this->secret = $settings['mailgunSecret'] ?? throw new InvalidArgumentException('"mailgunSecret" is required');
        $this->from = $settings['mailgunFromAddress'] ?? throw new InvalidArgumentException('"mailgunFromAddress" is required');
        $this->name = $settings['mailgunFromName'] ?? throw new InvalidArgumentException('"mailgunFromName" is required');
    }

    /**
     * @param array $parameters
     *
     * @return SendResponse|ResponseInterface
     */
    public function send(array $parameters): SendResponse|ResponseInterface
    {
        $parameters += ['from' => $this->from];

        return Mailgun::create($this->secret)->messages()->send($this->domain, $parameters);
    }
}
