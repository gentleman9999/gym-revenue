<?php

declare(strict_types=1);

namespace App\Domain\Templates\EmailTemplates\Projections;

use App\Domain\Templates\Services\Interfaces\TemplateParserInterface;
use App\Domain\Templates\Services\Traits\TemplateParserTrait;
use App\Domain\Users\Models\User;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * @property string $markup
 */
class EmailTemplate extends GymRevProjection implements TemplateParserInterface
{
    use SoftDeletes;
    use Sortable;
    use TemplateParserTrait;

    /** @var array<string> */
    protected $fillable = [
        'name',
        'markup',
        'subject',
        'thumbnail',
        'details',
        'json',
        'active',
        'team_id',
        'created_by_user_id',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'details' => 'array',
        'json' => 'array',
        'thumbnail' => 'array',
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

    public function gateway(): array
    {
        return $this->details['email_gateway'];
    }

    /**
     * attribute to retrieve the json field as a string (for graphql
     */
    public function getJsonStringAttribute(): string
    {
        return json_encode($this->json);
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany('App\Models\File', 'fileable');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
        static::updating(function ($model): void {
            if ($model->getOriginal()['markup'] !== $model->markup) {
                //markup changed, so reset the thumbnail if exists
                if ($model->thumbnail !== null) {
                    $thumbnail        = $model->thumbnail;
                    $thumbnail['url'] = null;
                    $model->thumbnail = $thumbnail;
                    if ($thumbnail['key'] !== null) {
                        Storage::disk('s3')->delete($thumbnail['key']);
                    }
                }
            }
        });
    }
}
