<?php

namespace App\Domain\AgreementTemplates;

use App\Domain\AgreementTemplates\Events\AgreementTemplateCreated;
use App\Domain\AgreementTemplates\Events\AgreementTemplateDeleted;
use App\Domain\AgreementTemplates\Events\AgreementTemplateRestored;
use App\Domain\AgreementTemplates\Events\AgreementTemplateTrashed;
use App\Domain\AgreementTemplates\Events\AgreementTemplateUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class AgreementTemplateAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new AgreementTemplateCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new AgreementTemplateTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new AgreementTemplateRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new AgreementTemplateDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new AgreementTemplateUpdated($payload));

        return $this;
    }
}
