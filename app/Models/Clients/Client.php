<?php

namespace App\Models\Clients;

use App\Enums\ClientServiceEnum;
use App\Enums\SecurityGroupEnum;
use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\Models\Endusers\LeadSource;
use App\Models\Endusers\LeadType;
use App\Models\Endusers\MembershipType;
use App\Models\Team;
use App\Models\User;
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
        'services',
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

    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst($value),
        );
    }

    public function setActiveAttribute(bool|int|string|null $val)
    {
        if (! isset($val) || ! $val || $val === 'false') {
            $this->attributes['active'] = 0;

            return;
        }
        $this->attributes['active'] = 1;
    }

    public function getServicesAttribute($value)
    {
        $services = collect(json_decode($value))->map(function ($service) {
            return ClientServiceEnum::tryFrom($service);
        })->filter()->toArray();

        return $services;
    }
}
