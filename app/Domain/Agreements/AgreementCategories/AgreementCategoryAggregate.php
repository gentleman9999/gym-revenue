<?php

declare(strict_types=1);

namespace App\Domain\Agreements\AgreementCategories;

use App\Domain\Agreements\AgreementCategories\Events\AgreementCategoryCreated;
use App\Domain\Agreements\AgreementCategories\Events\AgreementCategoryDeleted;
use App\Domain\Agreements\AgreementCategories\Events\AgreementCategoryRestored;
use App\Domain\Agreements\AgreementCategories\Events\AgreementCategoryTrashed;
use App\Domain\Agreements\AgreementCategories\Events\AgreementCategoryUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class AgreementCategoryAggregate extends AggregateRoot
{
    public function applyAgreementCategoryCreated(AgreementCategoryCreated $event): void
    {
        $this->agreement = $event->payload;
    }

    public function create(array $payload): static
    {
        $this->recordThat(new AgreementCategoryCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new AgreementCategoryTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new AgreementCategoryRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new AgreementCategoryDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new AgreementCategoryUpdated($payload));

        return $this;
    }
}
