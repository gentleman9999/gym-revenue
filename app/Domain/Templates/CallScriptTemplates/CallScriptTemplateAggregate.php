<?php

namespace App\Domain\Templates\CallScriptTemplates;

use App\Domain\Templates\CallScriptTemplates\Events\CallScriptTemplateCreated;
use App\Domain\Templates\CallScriptTemplates\Events\CallScriptTemplateThumbnailUpdated;
use App\Domain\Templates\CallScriptTemplates\Events\CallScriptTemplateUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class CallScriptTemplateAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new CallScriptTemplateCreated($payload));

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new CallScriptTemplateUpdated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new CampaignTemplateTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new CampaignTemplateRestored());

        return $this;
    }

    public function deleted(): static
    {
        $this->recordThat(new CampaignTemplateDeleted());

        return $this;
    }

    public function setThumbnail(string $key, string $url)
    {
        $this->recordThat(new CallScriptTemplateThumbnailUpdated($key, $url));

        return $this;
    }
}
