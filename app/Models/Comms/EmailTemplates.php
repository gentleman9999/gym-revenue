<?php

namespace App\Models\Comms;

use App\Models\Traits\Sortable;
use App\Models\User;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailTemplates extends Model
{
    use SoftDeletes;
    use Uuid;
    use Sortable;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id', 'client_id', 'name', 'markup', 'subject',
        'json', 'active', 'team_id', 'created_by_user_id',
    ];

    protected $casts = [
        'json' => 'array',
    ];

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
