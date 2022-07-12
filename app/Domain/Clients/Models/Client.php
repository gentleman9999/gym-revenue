<?php

namespace App\Domain\Clients\Models;

use App\Domain\LeadSources\LeadSource;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use App\Enums\SecurityGroupEnum;
use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\Models\Clients\Location;
use App\Models\Endusers\MembershipType;
use App\Models\GymRevProjection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Client extends GymRevProjection
{
    use Notifiable;
    use SoftDeletes;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'services' => 'array',
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
        return $this->hasMany(\App\Domain\LeadTypes\LeadType::class);
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

    public function home_team()
    {
        return $this->hasOne(Team::class, 'id', 'home_team_id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function accountOwners()
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('roles.scope', '=', $this->id)->whereGroup(SecurityGroupEnum::ACCOUNT_OWNER);
        })->orderBy('created_at');
    }
}
