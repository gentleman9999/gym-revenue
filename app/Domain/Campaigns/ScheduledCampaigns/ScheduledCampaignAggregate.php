<?php

namespace App\Domain\Campaigns\ScheduledCampaigns;

use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBeCompleted;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBeLaunched;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBePublished;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBeUnpublished;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignCompleted;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignCreated;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignDeleted;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignLaunched;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignPublished;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignRestored;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignTrashed;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignUnpublished;
use App\Domain\Campaigns\ScheduledCampaigns\Events\ScheduledCampaignUpdated;
use Carbon\CarbonImmutable;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class ScheduledCampaignAggregate extends AggregateRoot
{
    protected CampaignStatusEnum $status;
    protected string $client_id;
    protected array $data;

    public function __construct()
    {
        //$this->status = CampaignStatusEnum::DRAFT;
    }

    public function create(array $payload): static
    {
        $this->recordThat(new ScheduledCampaignCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new ScheduledCampaignTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new ScheduledCampaignRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new ScheduledCampaignDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $send_at = CarbonImmutable::create($this->data['send_at']);

        if (! $this->isDraft() && $send_at <= CarbonImmutable::now()->addMinutes(-1)) {
            //campaign is not a draft and should have already been started.
            //only allow updating name
            $payload = array_filter_only_keys($payload, ['name']);
        }

        $this->recordThat(new ScheduledCampaignUpdated($payload));

        return $this;
    }

    /**
     * Publishes the campaign (set to status to PENDING).
     * @return $this
     * @throws CampaignCouldNotBePublished
     */
    public function publish(): static
    {
        if (! $this->isDraft()) {
            $expected = CampaignStatusEnum::DRAFT->name;
            $actual = $this->status->name;
            $msg = "[ScheduledCampaign id '{$this->uuid()}'] expected status to be '$expected', but was provided '$actual'";

            throw new CampaignCouldNotBePublished($msg);
        }

        $this->recordThat(new ScheduledCampaignPublished());

        return $this;
    }

    /**
     * Unpublishes the campaign (set to status to DRAFT).
     * @return $this
     * @throws CampaignCouldNotBeUnpublished
     */
    public function unpublish(): static
    {
        if (! $this->isPending()) {
            $expected = CampaignStatusEnum::PENDING->name;
            $actual = $this->status->name;
            $msg = "[ScheduledCampaign id '{$this->uuid()}'] expected status to be '$expected', but was provided '$actual'";

            throw new CampaignCouldNotBeUnpublished($msg);
        }

        $this->recordThat(new ScheduledCampaignUnpublished());

        return $this;
    }

    /**
     * Launches the campaign.
     * @return $this
     * @throws CampaignCouldNotBeLaunched
     */
    public function launch(): static
    {
        if (! $this->isPending()) {
            $expected = CampaignStatusEnum::PENDING->name;
            $actual = $this->status->name;
            $msg = "[ScheduledCampaign id '{$this->uuid()}'] expected status to be '$expected', but was provided '$actual'";

            throw new CampaignCouldNotBeLaunched($msg);
        }

        $now = CarbonImmutable::now();
        $send_at = CarbonImmutable::create($this->data['send_at']);
        if ($now < $send_at) {
            $msg = "[ScheduledCampaign id '{$this->uuid()}'] tried to launch at $now, but can't launch before '$send_at'.";

            throw new CampaignCouldNotBeLaunched($msg);
        }

        $this->recordThat(new ScheduledCampaignLaunched());

        return $this;
    }

    /**
     * Completes the campaign.
     * @return $this
     * @throws CampaignCouldNotBeCompleted
     */
    public function complete(): static
    {
        if (! $this->isActive()) {
            $expected = CampaignStatusEnum::ACTIVE->name;
            $actual = $this->status->name;
            $msg = "[ScheduledCampaign id '{$this->uuid()}'] expected status to be '$expected', but was provided '$actual'";

            throw new CampaignCouldNotBeCompleted($msg);
        }
        $this->recordThat(new ScheduledCampaignCompleted());

        return $this;
    }

    public function applyOnScheduledCampaignCreated(ScheduledCampaignCreated $event): void
    {
        $data = array_filter_only_fillable($event->payload, ScheduledCampaign::class);
        if (array_key_exists('client_id', $event->payload)) {
            $this->client_id = $event->payload['client_id'];
        }
        $this->data = $data;
    }

    public function applyOnScheduledCampaignUpdated(ScheduledCampaignUpdated $event): void
    {
        $this->data = array_merge(array_filter_only_fillable($this->data, ScheduledCampaign::class), $event->payload);
    }

    public function applyOnScheduledCampaignPublished(ScheduledCampaignPublished $event): void
    {
        $this->status = CampaignStatusEnum::PENDING;
    }

    public function applyOnScheduledCampaignUnpublished(ScheduledCampaignUnpublished $event): void
    {
        $this->status = CampaignStatusEnum::DRAFT;
    }

    public function applyOnScheduledCampaignLaunched(ScheduledCampaignLaunched $event): void
    {
        $this->status = CampaignStatusEnum::ACTIVE;
    }

    public function applyOnScheduledCampaignCompleted(ScheduledCampaignCompleted $event): void
    {
        $this->status = CampaignStatusEnum::COMPLETED;
    }

    public function isDraft(): bool
    {
        return $this->status === CampaignStatusEnum::DRAFT;
    }

    public function isPending(): bool
    {
        return $this->status === CampaignStatusEnum::PENDING;
    }

    public function isActive(): bool
    {
        return $this->status === CampaignStatusEnum::ACTIVE;
    }

    public function isCompleted(): bool
    {
        return $this->status === CampaignStatusEnum::COMPLETED;
    }

    public function getStatus(): CampaignStatusEnum
    {
        return $this->status;
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
