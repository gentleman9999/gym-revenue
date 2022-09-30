<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplateBlocks;

use App\Domain\Templates\EmailTemplateBlocks\Events\EmailTemplateBlockCreated;
use App\Domain\Templates\EmailTemplateBlocks\Events\EmailTemplateBlockDeleted;
use App\Domain\Templates\EmailTemplateBlocks\Events\EmailTemplateBlockUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class EmailTemplateBlockAggregates extends AggregateRoot
{
    public function emailTemplateBlockCreated(array $payload): static
    {
        $this->recordThat(new EmailTemplateBlockCreated($payload));

        return $this;
    }

    public function emailTemplateBlockUpdated(array $payload): static
    {
        $this->recordThat(new EmailTemplateBlockUpdated($payload));

        return $this;
    }

    public function emailTemplateBlockDeleted(): static
    {
        $this->recordThat(new EmailTemplateBlockDeleted());

        return $this;
    }
}
