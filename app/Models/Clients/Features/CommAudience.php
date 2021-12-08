<?php

namespace App\Models\Clients\Features;

use App\Models\Clients\ClientDetail;
use App\Models\Endusers\AudienceMember;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class CommAudience extends Model
{
    use Notifiable, SoftDeletes, Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name','slug', 'active', 'client_id', 'team_id', 'created_by_user_id'
    ];

    public function details()
    {
        return $this->hasMany(AudienceDetails::class, 'audience_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne(AudienceDetails::class, 'audience_id', 'id');
    }

    public function subscribers()
    {
        return $this->hasMany(AudienceMember::class, 'audience_id', 'id');
    }

    public function active_subscribers()
    {
        return $this->subscribers()->whereActive(1);
    }
}
