<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\DripCampaigns;

use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayCompleted;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayCreated;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayDeleted;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayLaunched;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayPublished;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayRestored;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayTrashed;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayUnpublished;
use App\Domain\Campaigns\DripCampaigns\Events\DripCampaignDayUpdated;
use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBeCompleted;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBeLaunched;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBePublished;
use App\Domain\Campaigns\Exceptions\CampaignCouldNotBeUnpublished;
use Carbon\CarbonImmutable;
use Spatie\EventSourcing\AggregateRoots\AggregateRoot;

class DripCampaignDayAggregate extends AggregateRoot
{
    protected CampaignStatusEnum $status;
    protected array $data;
    protected string $drip_campaign_id;

    public function __construct()
    {
        $this->status = CampaignStatusEnum::DRAFT;
    }

    public function create(array $payload): static
    {
        $this->recordThat(new DripCampaignDayCreated($payload));

        return $this;
    }

    public function trash(): static
    {
        $this->recordThat(new DripCampaignDayTrashed());

        return $this;
    }

    public function restore(): static
    {
        $this->recordThat(new DripCampaignDayRestored());

        return $this;
    }

    public function delete(): static
    {
        $this->recordThat(new DripCampaignDayDeleted());

        return $this;
    }

    public function update(array $payload): static
    {
        $send_at = null;
        if (array_key_exists('send_at', $this->data)) {
            $send_at = CarbonImmutable::create($this->data['send_at']);
        }

        if (! $this->isDraft() && $send_at && $send_at <= CarbonImmutable::now()->addMinutes(-1)) {
            //campaign is not a draft and should have already been started.
            //only allow updating name
            $payload = array_filter_only_keys($payload, ['name']);
        }

        $this->recordThat(new DripCampaignDayUpdated($payload));

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
            $actual   = $this->status->name;
            $msg      = "[DripCampaign id '{$this->uuid()}'] expected status to be '$expected', but was provided '$actual'";

            throw new CampaignCouldNotBePublished($msg);
        }

        $this->recordThat(new DripCampaignDayPublished());

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
            $actual   = $this->status->name;
            $msg      = "[DripCampaign id '{$this->uuid()}'] expected status to be '$expected', but was provided '$actual'";

            throw new CampaignCouldNotBeUnpublished($msg);
        }

        $this->recordThat(new DripCampaignDayUnpublished());

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
            $actual   = $this->status->name;
            $msg      = "[DripCampaign id '{$this->uuid()}'] expected status to be '$expected', but was provided '$actual'";

            throw new CampaignCouldNotBeLaunched($msg);
        }

        $this->recordThat(new DripCampaignDayLaunched());

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
            $actual   = $this->status->name;
            $msg      = "[DripCampaign id '{$this->uuid()}'] expected status to be '$expected', but was provided '$actual'";

            throw new CampaignCouldNotBeCompleted($msg);
        }
        $this->recordThat(new DripCampaignDayCompleted());

        return $this;
    }

    public function applyOnDripCampaignDayCreated(DripCampaignDayCreated $event): void
    {
        $data = array_filter_only_fillable($event->payload, DripCampaignDay::class);
        if (array_key_exists('drip_campaign_id', $event->payload)) {
            $this->drip_campaign_id = $event->payload['drip_campaign_id'];
        }
        $this->data = $data;
    }

    public function applyOnDripCampaignDayUpdated(DripCampaignDayUpdated $event): void
    {
        $this->data = array_merge(array_filter_only_fillable($this->data, DripCampaignDay::class), $event->payload);
    }

    public function applyOnDripCampaignDayPublished(DripCampaignDayPublished $event): void
    {
        $this->status = CampaignStatusEnum::PENDING;
    }

    public function applyOnDripCampaignDayUnpublished(DripCampaignDayUnpublished $event): void
    {
        $this->status = CampaignStatusEnum::DRAFT;
    }

    public function applyOnDripCampaignDayLaunched(DripCampaignDayLaunched $event): void
    {
        $this->status = CampaignStatusEnum::ACTIVE;
    }

    public function applyOnDripCampaignDayCompleted(DripCampaignDayCompleted $event): void
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

    public function getDripCampaignId(): ?string
    {
        return $this->drip_campaign_id;
    }
}
