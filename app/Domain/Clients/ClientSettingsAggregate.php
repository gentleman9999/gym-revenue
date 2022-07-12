<?php

namespace App\Domain\Clients;

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

    public function uploadLogo(array $payload): static
    {
        $this->recordThat(new ClientLogoUploaded($payload));

        return $this;
    }
}
