<?php

namespace App\Domain\Templates\EmailTemplates\Projections;

use App\Domain\Users\Models\User;
use App\Models\GymRevProjection;
use App\Models\Traits\Sortable;
use App\Scopes\ClientScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class EmailTemplate extends GymRevProjection
{
    use SoftDeletes;
    use Sortable;

    protected $fillable = [
        'name', 'markup', 'subject', 'thumbnail',
        'json', 'active', 'team_id', 'created_by_user_id',
    ];

    protected $casts = [
        'json' => 'array',
        'thumbnail' => 'array',
    ];

    protected static function booted()
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
        $this->attributes['markup'] = base64_encode($value);
    }

    public function creator()
    {
        return $this->hasOne(User::class, 'id', 'created_by_user_id');
    }

    public function details()
    {
        return $this->hasMany(EmailTemplateDetails::class, 'email_template_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne(EmailTemplateDetails::class, 'email_template_id', 'id');
    }

    public function gateway()
    {
        return $this->detail()->whereDetail('email_gateway')->whereActive(1);
    }
}
