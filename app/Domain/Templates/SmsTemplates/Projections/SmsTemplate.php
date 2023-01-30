<?php

declare(strict_types=1);

namespace App\Domain\Templates\SmsTemplates\Projections;

use App\Domain\Templates\Services\Interfaces\TemplateParserInterface;
use App\Domain\Templates\Services\Traits\TemplateParserTrait;
use App\Domain\Templates\SmsTemplates\SmsTemplateAggregate;
use App\Domain\Users\Models\User;
use App\Models\GymRevProjection;
use App\Models\Traits\Duplicateable;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsTemplate extends GymRevProjection implements TemplateParserInterface
{
    use SoftDeletes;
    use Sortable;
    use TemplateParserTrait;
    use Duplicateable;

    protected $fillable = [
        'name', 'markup', 'active', 'details',
        'team_id', 'created_by_user_id',
    ];

    protected $casts = [
        'details' => 'array',
    ];

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

    public function setMarkupAttribute($value): void
    {
        $this->attributes['markup'] = base64_encode($value);
    }

    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by_user_id');
    }

    //TODO: SMS Templates should not know about gateways. Move logic to campaigns.
    public function gateway(): array
    {
        return $this->details['sms_gateway'];
    }

    public static function getAggregate(): SmsTemplateAggregate
    {
        return new SmsTemplateAggregate();
    }
}
