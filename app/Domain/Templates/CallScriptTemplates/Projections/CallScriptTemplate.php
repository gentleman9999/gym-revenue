<?php

declare(strict_types=1);

namespace App\Domain\Templates\CallScriptTemplates\Projections;

use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallScriptTemplate extends GymRevProjection
{
    use SoftDeletes;
    use Sortable;

    /** @var array<string> */
    protected $fillable = ['name', 'script', 'active', 'use_once', 'team_id', 'created_by_user_id'];

    /** @var array<string, string> */
    protected $casts = [
        'name' => 'string',
    ];

    /**
     * @param array<string, mixed> $filters
     *
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['search'] ?? null, function ($query, $search): void {
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('created_by_user_id', 'like', '%' . $search . '%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed): void {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function getMarkupAttribute($value)
    {
        return base64_decode($value);
    }

    public function setMarkupAttribute($value): void
    {
        $this->attributes['script'] = base64_encode($value);
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by_user_id');
    }

    public function details()
    {
        return $this;
    }

    public function detail()
    {
        return $this;
    }

    public function gateway()
    {
        return $this->detail()->whereDetail('campaign_gateway')->whereActive(1);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
