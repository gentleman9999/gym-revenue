<?php

namespace App\Models\Clients;

use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\Models\Endusers\LeadSource;
use App\Models\Endusers\LeadType;
use App\Models\Endusers\MembershipType;
use App\Models\Team;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use Notifiable;
    use SoftDeletes;
    use Uuid;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'active',
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function details()
    {
        return $this->hasMany(ClientDetail::class);
    }

    public function lead_types()
    {
        return $this->hasMany(LeadType::class);
    }

    public function lead_sources()
    {
        return $this->hasMany(LeadSource::class);
    }

    public function membership_types()
    {
        return $this->hasMany(MembershipType::class);
    }

    public function trial_membership_types()
    {
        return $this->hasMany(TrialMembershipType::class);
    }

    public function detail()
    {
        return $this->hasOne(ClientDetail::class);
    }

    public function default_team_name()
    {
        return $this->detail()->where('detail', '=', 'default-team');
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function services()
    {
        return $this->details()->whereDetail('service_slug')->whereActive(1);
    }
}
