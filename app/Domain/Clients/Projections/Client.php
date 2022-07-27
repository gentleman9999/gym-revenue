<?php

namespace App\Domain\Clients\Projections;

use App\Domain\Clients\Models\ClientSocialMedia;
use App\Domain\LeadSources\LeadSource;
use App\Domain\Locations\Projections\Location;
use App\Domain\Teams\Models\Team;
use App\Domain\Users\Models\User;
use App\Enums\SecurityGroupEnum;
use App\Models\Clients\Features\Memberships\TrialMembershipType;
use App\Models\Endusers\MembershipType;
use App\Models\File;
use App\Models\GymRevProjection;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Client extends GymRevProjection
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'active',
    ];

    protected $casts = [
        'services' => 'array',
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ClientDetail::class);
    }

    public function lead_types(): HasMany
    {
        return $this->hasMany(\App\Domain\LeadTypes\LeadType::class);
    }

    public function lead_sources(): HasMany
    {
        return $this->hasMany(LeadSource::class);
    }

    public function membership_types(): HasMany
    {
        return $this->hasMany(MembershipType::class);
    }

    public function trial_membership_types(): HasMany
    {
        return $this->hasMany(TrialMembershipType::class);
    }

    public function detail(): HasOne
    {
        return $this->hasOne(ClientDetail::class);
    }

    public function home_team(): HasOne
    {
        return $this->hasOne(Team::class, 'id', 'home_team_id');
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function socialMedia(): HasMany
    {
        return $this->hasMany(ClientSocialMedia::class, );
    }

    public function accountOwners(): Collection
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('roles.scope', '=', $this->id)->whereGroup(SecurityGroupEnum::ACCOUNT_OWNER);
        })->orderBy('created_at')->get();
    }

    public function logo_url(): ?string
    {
        $file = File::whereType('logo')->first();

        return is_null($file) ? null : $file->url;
    }

    public function getSocialMedia(): Collection
    {
        return $this->socialMedia->keyBy('name')->map(fn ($s) => $s->value);
    }
}
