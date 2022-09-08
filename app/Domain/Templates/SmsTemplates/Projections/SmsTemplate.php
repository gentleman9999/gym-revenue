<?php

namespace App\Domain\Templates\SmsTemplates\Projections;

use App\Domain\Users\Models\User;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsTemplate extends GymRevProjection
{
    use SoftDeletes;
    use Sortable;

    protected $fillable = [ 'name', 'markup', 'active', 'team_id', 'created_by_user_id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }

    public function scopeFilter($query, array $filters): void
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

    public function getMarkupAttribute($value): false|string
    {
        return base64_decode($value);
    }

    public function setMarkupAttribute($value): void
    {
        $this->attributes['markup'] = base64_encode($value);
    }

    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by_user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(SmsTemplateDetails::class, 'sms_template_id', 'id');
    }

    public function detail(): HasOne
    {
        return $this->hasOne(SmsTemplateDetails::class, 'sms_template_id', 'id');
    }

    public function gateway(): HasOne
    {
        return $this->detail()->whereDetail('sms_gateway')->whereActive(1);
    }
}
