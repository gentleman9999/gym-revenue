<?php

namespace App\Domain\Campaigns\DripCampaigns;

use App\Domain\Audiences\Audience;
use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Clients\Models\Client;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class DripCampaign extends GymRevProjection
{
    use SoftDeletes;
    use Sortable;

    protected $hidden = ['client_id'];

    protected $fillable = ['name', 'audience_id', 'start_at', 'end_at'];

    protected $appends = ['is_published', 'can_publish', 'can_unpublish'];

    protected $casts = [
        'status' => CampaignStatusEnum::class,
        'start_at' => 'immutable_datetime',
        'end_at' => 'immutable_datetime',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

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
        if ($this->start_at <= CarbonImmutable::now()->addMinute(-1)) {
            return false;
        }

        return true;
    }

    public function getCanUnpublishAttribute(): bool
    {
        return $this->can_publish;
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }
}
