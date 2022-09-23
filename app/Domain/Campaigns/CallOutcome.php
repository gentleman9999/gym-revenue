<?php

namespace App\Domain\Campaigns;

use App\Domain\Audiences\Audience;
use App\Domain\Clients\Projections\Client;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallOutcome extends GymRevProjection
{
    use SoftDeletes;
    use Sortable;

    protected $hidden = ['client_id'];

    protected $fillable = ['client_id','field', 'value', 'misc', 'active', 'lead_id', 'member_id'];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function audience(): BelongsTo
    {
        return $this->belongsTo(Audience::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function isLead()
    {
        return $this->lead();
    }

    public function isMember()
    {
        return $this->member();
    }

    public function scopeFilter($query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('field', 'like', '%' . $search . '%');
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
