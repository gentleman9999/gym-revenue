<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates;

use App\Domain\Templates\EmailTemplates\Events\EmailTemplateCreated;
use App\Domain\Templates\EmailTemplates\Events\EmailTemplateThumbnailUpdated;
use App\Domain\Templates\EmailTemplates\Events\EmailTemplateTrashed;
use App\Domain\Templates\EmailTemplates\Events\EmailTemplateUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class EmailTemplateAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new EmailTemplateCreated($payload));

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new EmailTemplateUpdated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new EmailTemplateTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new EmailTemplateRestored());

        return $this;
    }

    public function deleted(): static
    {
        $this->recordThat(new EmailTemplateDeleted());

        return $this;
    }

    public function setThumbnail(string $key, string $url)
    {
        $this->recordThat(new EmailTemplateThumbnailUpdated($key, $url));

        return $this;
    }
}
