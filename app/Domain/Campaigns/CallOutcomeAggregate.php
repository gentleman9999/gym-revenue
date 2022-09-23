<?php

namespace App\Domain\Campaigns;

use App\Domain\Campaigns\Events\CallOutcomeCreated;
use App\Domain\Campaigns\Events\CallOutcomeUpdated;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class CallOutcomeAggregate extends AggregateRoot
{
    protected string $client_id;
    protected array $data;

    public function create(array $payload): static
    {
        $this->recordThat(new CallOutcomeCreated($payload));

        return $this;
    }

    public function update(array $payload): static
    {
        $this->recordThat(new CallOutcomeUpdated($payload));

        return $this;
    }

    public function applyOnCallOutcomeCreated(CallOutcomeCreated $event): void
    {
        $data = array_filter_only_fillable($event->payload, CallOutcome::class);
        $this->client_id = $event->payload['client_id'] ?? null;
        $this->data = $data;
    }

    public function applyOnCallOutcomeUpdated(CallOutcomeUpdated $event): void
    {
        $data = array_filter_only_fillable($event->payload, CallOutcome::class);
        $this->client_id = $event->payload['client_id'] ?? null;
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getClientId(): ?string
    {
        return $this->client_id;
    }
}
