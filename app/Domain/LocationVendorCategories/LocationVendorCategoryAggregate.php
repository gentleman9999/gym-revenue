<?php

declare(strict_types=1);

namespace App\Domain\LocationVendorCategories;

use App\Domain\LocationVendorCategories\Events\LocationVendorCategoryCreated;
use App\Domain\LocationVendorCategories\Events\LocationVendorCategoryDeleted;
use App\Domain\LocationVendorCategories\Events\LocationVendorCategoryRestored;
use App\Domain\LocationVendorCategories\Events\LocationVendorCategoryTrashed;
use App\Domain\LocationVendorCategories\Events\LocationVendorCategoryUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class LocationVendorCategoryAggregate extends AggregateRoot
{
    public function create(array $payload): static
    {
        $this->recordThat(new LocationVendorCategoryCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new LocationVendorCategoryTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new LocationVendorCategoryRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new LocationVendorCategoryDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new LocationVendorCategoryUpdated($payload));

        return $this;
    }
}
