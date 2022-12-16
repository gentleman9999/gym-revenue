<?php

namespace App\Domain\Templates\EmailTemplates\Projections;

use App\Domain\Templates\Services\Interfaces\TemplateParserInterface;
use App\Domain\Templates\Services\Traits\TemplateParserTrait;
use App\Domain\Users\Models\User;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    protected $fillable = [
        'name', 'markup', 'subject', 'thumbnail',
        'json', 'active', 'team_id', 'created_by_user_id',
    ];

    protected $casts = [
        'json' => 'array',
        'thumbnail' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ClientScope());
        static::updating(function ($model) {
            if ($model->getOriginal()['markup'] !== $model->markup) {
                //markup changed, so reset the thumbnail if exists
                if ($model->thumbnail !== null) {
                    $thumbnail = $model->thumbnail;
                    $thumbnail['url'] = null;
                    $model->thumbnail = $thumbnail;
                    if ($thumbnail['key'] !== null) {
                        Storage::disk('s3')->delete($thumbnail['key']);
                    }
                }
            }
        });
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

    public function details(): HasMany
    {
        return $this->hasMany(EmailTemplateDetails::class, 'email_template_id', 'id');
    }

    public function detail(): HasOne
    {
        return $this->hasOne(EmailTemplateDetails::class, 'email_template_id', 'id');
    }

    public function gateway(): HasOne
    {
        return $this->detail()->whereDetail('email_gateway')->whereActive(1);
    }

    /**
     * attribute to retrieve the json field as a string (for graphql
     * @return string
     */
    public function getJsonStringAttribute(): string
    {
        return json_encode($this->json);
    }
}
