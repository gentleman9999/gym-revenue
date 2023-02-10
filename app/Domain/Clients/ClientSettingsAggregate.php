<?php

declare(strict_types=1);

namespace App\Domain\Clients;

use App\Domain\Clients\Events\ClientGatewaySet;
use App\Domain\Clients\Events\ClientLogoDeleted;
use App\Domain\Clients\Events\ClientLogoUploaded;
use App\Domain\Clients\Events\ClientSocialMediaSet;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ClientSettingsAggregate extends AggregateRoot
{
    public function setSocialMedias(array $payload): static
    {
        $this->recordThat(new ClientSocialMediaSet($payload));

        return $this;
    }

    public function setGateways(array $payload): static
    {
        $this->recordThat(new ClientGatewaySet($payload));

        return $this;
    }

    public function uploadLogo(array $payload): static
    {
        $this->recordThat(new ClientLogoUploaded($payload));

        return $this;
    }

    public function deleteLogo(array $payload): static
    {
        $this->recordThat(new ClientLogoDeleted($payload));

        return $this;
    }
}
