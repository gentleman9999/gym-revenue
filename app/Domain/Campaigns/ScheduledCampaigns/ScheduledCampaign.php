<?php

namespace App\Domain\Campaigns\ScheduledCampaigns;

use App\Domain\Audiences\Audience;
use App\Domain\Campaigns\Enums\CampaignStatusEnum;
use App\Domain\Clients\Projections\Client;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\EventSourcing\Projections\Projection;

class ScheduledCampaign extends Projection
{
    use SoftDeletes;
    use Sortable;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = ['client_id'];

    protected $fillable = ['name', 'audience_id', 'send_at', 'email_template_id', 'sms_template_id', 'call_template_id', 'template_type', 'template_id'];

    protected $appends = ['is_published', 'can_publish', 'can_unpublish', 'daysCount'];

    protected $casts = [
        'status' => CampaignStatusEnum::class,
        'send_at' => 'immutable_datetime',
    ];

    public function getKeyName(): string
    {
        return 'id';
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }

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
        if ($this->send_at && $this->send_at <= CarbonImmutable::now()->addMinute(-1)) {
            return false;
        }

        return true;
    }

    public function getCanUnpublishAttribute(): bool
    {
        return $this->can_publish;
    }

    public function getDaysCountAttribute(): int
    {
        return 1;
    }

    public function getEntity(): string
    {
        return ScheduledCampaign::class;
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
