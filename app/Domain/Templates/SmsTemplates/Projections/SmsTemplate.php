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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SmsTemplate extends GymRevProjection implements TemplateParserInterface
{
    use SoftDeletes;
    use Sortable;
    use TemplateParserTrait;
    use Duplicateable;

    /** @var array<string> */
    protected $fillable = [
        'name',
        'markup',
        'active',
        'details',
        'team_id',
        'created_by_user_id',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'details' => 'array',
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

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
    }
}
