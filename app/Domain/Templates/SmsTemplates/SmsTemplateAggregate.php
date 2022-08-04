<?php

namespace App\Domain\Templates\SmsTemplates;

use App\Domain\Templates\SmsTemplates\Events\SmsTemplateCreated;
use App\Domain\Templates\SmsTemplates\Events\SmsTemplateUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class SmsTemplateAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new SmsTemplateCreated($payload));

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new SmsTemplateUpdated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new SmsTemplateTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new SmsTemplateRestored());

        return $this;
    }

    public function deleted(): static
    {
        $this->recordThat(new SmsTemplateDeleted());

        return $this;
    }
}
