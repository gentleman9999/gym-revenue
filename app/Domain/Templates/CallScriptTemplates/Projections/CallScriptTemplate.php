<?php

namespace App\Domain\Templates\CallScriptTemplates\Projections;

use App\Domain\Users\Models\User;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallScriptTemplate extends GymRevProjection
{
    use SoftDeletes;
    use Sortable;

    protected $fillable = [ 'name', 'script', 'active', 'use_once', 'team_id', 'created_by_user_id'];

    protected $casts = [
        'name' => 'string',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new ClientScope());
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('created_by_user_id', 'like', '%' . $search . '%')
                ;
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
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

    public function setMarkupAttribute($value)
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
}
