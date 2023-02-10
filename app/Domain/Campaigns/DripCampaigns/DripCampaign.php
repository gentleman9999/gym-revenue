<?php

declare(strict_types=1);

namespace App\Domain\Campaigns\DripCampaigns;

use App\Domain\Audiences\Audience;
use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Clients\Projections\Client;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DripCampaign extends GymRevProjection
{
    use SoftDeletes;
    use Sortable;

    /** @var array<string> */
    protected $hidden = ['client_id'];

    /** @var array<string> */
    protected $fillable = ['client_id', 'name', 'audience_id', 'start_at', 'end_at', 'completed_at', 'status'];

    /** @var array<string> */
    protected $appends = ['is_published', 'can_publish', 'can_unpublish', 'daysCount'];

    /** @var array<string, string> */
    protected $casts = [
        'status' => CampaignStatusEnum::class,
        'start_at' => 'immutable_datetime',
        'end_at' => 'immutable_datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function audience(): HasOne
    {
        return $this->hasOne(Audience::class);
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->status !== CampaignStatusEnum::DRAFT;
    }

    public function getCanPublishAttribute(): bool
    {
        if ($this->status === CampaignStatusEnum::COMPLETED || $this->status === CampaignStatusEnum::ACTIVE) {
            return false;
        }

        if ($this->start_at && $this->start_at <= CarbonImmutable::now()->addMinute(-1)) {
            return false;
        }

        return true;
    }

    public function getCanUnpublishAttribute(): bool
    {
        return $this->can_publish;
    }

    /**
     * @param array<string, mixed> $filters
     *
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search): void {
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed): void {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function getDaysCountAttribute(): int
    {
        return count($this->days);
    }

    public function getEntity(): string
    {
        return DripCampaign::class;
    }

    public function days()
    {
        return $this->hasMany(DripCampaignDay::class)->orderBy('day_of_campaign');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
